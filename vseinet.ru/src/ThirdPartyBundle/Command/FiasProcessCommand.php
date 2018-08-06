<?php

namespace ThirdPartyBundle\Command;

use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ThirdPartyBundle\Components\FiasComponent;
use ThirdPartyBundle\Entity\{GeoRegion, GeoCity, GeoStreet, GeoTemp};

class FiasProcessCommand extends ContainerAwareCommand
{
    /**
     * @var FiasComponent $_component
     */
    private $_component;
    private $_parser;
    private $_currentRow = 0;
    private $_treeDepth = 0;
    private $_queue = [];

    /**
     * @param FiasComponent $component
     */
    public function setComponent(FiasComponent $component)
    {
        $this->_component = $component;
    }

    /**
     * @return FiasComponent
     */
    public function getComponent() : FiasComponent
    {
        return $this->_component;
    }

    protected function configure() : void
    {
        $this->setName('fias:process')->setDescription('Process data from temp table to geo tables')
            ->addArgument('type', InputArgument::REQUIRED, 'Table type ('.GeoUpdateCommand::TYPE_REGIONS.'|'.GeoUpdateCommand::TYPE_CITIES.'|'.GeoUpdateCommand::TYPE_STREETS.')');

        $this->_parser = xml_parser_create();
        xml_parser_set_option($this->_parser, XML_OPTION_CASE_FOLDING, false);
        xml_set_object($this->_parser, $this);
        xml_set_element_handler($this->_parser, 'parserOpenTag', 'parserClosetag');
    }

    /**
     * @param $parser
     * @param $tag
     * @param $attributes
     *
     * @return int
     */
    private function parserOpenTag($parser, $tag, $attributes)
    {
        // Пропускаем родительский элемент
        if ($this->_treeDepth === 0) {
            return $this->_treeDepth += 1; // псевдо-дерево
        }

        $this->_treeDepth += 1;
        $this->_currentRow += 1;

        // Поля записи
        $this->_queue[] = $attributes;
    }

    /**
     * @param $parser
     * @param $tag
     */
    private function parserCloseTag($parser, $tag) : void
    {
        $this->_treeDepth -= 1; // псевдо-дерево
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $time = time();

        $type = strval($input->getArgument('type'));

        if (!in_array($type, [GeoUpdateCommand::TYPE_REGIONS, GeoUpdateCommand::TYPE_CITIES, GeoUpdateCommand::TYPE_STREETS,])) {
            throw new \Exception('Неверный тип таблицы: '.$type);
        }
        
        $this->setComponent((new FiasComponent($output, $this->getContainer()->get('doctrine.orm.entity_manager'))));

        try {
            switch ($type) {
                case GeoUpdateCommand::TYPE_REGIONS:
                    $this->_processRegionTable();
                    break;

                case GeoUpdateCommand::TYPE_CITIES:
                    $this->_processCityTable();
                    break;

                case GeoUpdateCommand::TYPE_STREETS:
                    $this->_processStreetTable();
                    break;
            }
        } catch (\Exception $e) {
            $this->getComponent()->getOutput()->writeln('ERROR: ' . $e->getMessage());
        }

        $this->getComponent()->getOutput()->writeln('Time remained: ' . (time() - $time) . ' sec');
    }

    /**
     * @throws \Exception
     */
    private function _processRegionTable() : void
    {
        $time = time();
        $this->getComponent()->getOutput()->writeln('===========================');
        $this->getComponent()->getOutput()->writeln('Process regions...' . $this->getComponent()->showMemUsage());

        $repository = $this->getComponent()->getEm()->getRepository('ThirdPartyBundle:GeoTemp');

        try {
            $query = $repository->createQueryBuilder('t')
                ->select(['t.id', 't.AOGUID', 't.OFFNAME', 't.SHORTNAME',])
                ->where('t.is_processed = :is_processed AND t.AOLEVEL = :AOLEVEL')
                ->setParameter('is_processed', GeoTemp::IS_PROCESSED_NO)
                ->setParameter('AOLEVEL', FiasComponent::AOLEVEL_1_REGION)
                ->getQuery();

            $items = $query->getArrayResult();

            $this->getComponent()->getOutput()->writeln('Regions count: ' . count($items) . $this->getComponent()->showMemUsage());

            foreach ($items as $item) {
                try {
                    $model = new GeoRegion();
                    $model->setName($item['OFFNAME']);
                    $model->setAOGUID($item['AOGUID']);
                    $model->setUnit($item['SHORTNAME']);

                    $this->getComponent()->getEm()->persist($model);

                    $this->getComponent()->setIsProcessed($item['id']);
                } catch (\Exception $e) {
                    $this->getComponent()->getOutput()->writeln('Region ' . $item['OFFNAME'] . ' (' . $item['AOGUID'] . ') NOT saved');
                }
            }

            $this->getComponent()->getEm()->flush();

            $this->getComponent()->getOutput()->writeln('Time remained: ' . (time() - $time) . ' sec.' . $this->getComponent()->showMemUsage());
        } catch (\Exception $e) {
            $this->getComponent()->getOutput()->writeln($e->getMessage() . $e->getTraceAsString());

            throw $e;
        }
    }

    private function _processCityTable() : void
    {
        $this->getComponent()->getOutput()->writeln('===========================');


        $sql = 'select max(id) as max_id from geo_city';

        $q = $this->getComponent()->getEm()->createNativeQuery($sql, new ResultSetMapping());

        $rows = $q->getResult('ListAssocHydrator');
        $row = array_shift($rows);

        $maxId = $row['max_id'];

        $this->getComponent()->getOutput()->writeln('Geo city max id = ' . $maxId);

        $added = 0;
        while(true) { // 193132
            $sql = '
                SELECT
                    gr.id AS geo_region_id,
                    gt.id,
                    gt."AOGUID",
                    gt."OFFNAME",
                    gt."CENTSTATUS",
                    gt."SHORTNAME" 
                FROM
                    geo_temp gt
                    INNER JOIN geo_temp gtp ON gtp."AOGUID" = gt."PARENTGUID"
                    LEFT JOIN geo_temp gtpp ON gtpp."AOGUID" = gtp."PARENTGUID"
                    LEFT JOIN geo_temp gtppp ON gtppp."AOGUID" = gtpp."PARENTGUID"
                    LEFT JOIN geo_region gr ON gr."AOGUID" = CASE
                        WHEN gtp."AOLEVEL" = :AOLEVEL1::INTEGER THEN	gtp."AOGUID" 
                        WHEN gtpp."AOLEVEL" = :AOLEVEL1::INTEGER THEN	gtpp."AOGUID" 
                        WHEN gtppp."AOLEVEL" = :AOLEVEL1::INTEGER THEN	gtppp."AOGUID" 
                    END 
                    LEFT JOIN geo_city gc ON gc."AOGUID" = gt."AOGUID"
                WHERE
                    gt."AOLEVEL" IN ( :AOLEVEL4::INTEGER, :AOLEVEL6::INTEGER ) 
                    AND gt.is_processed = :is_processed
                    AND gc."AOGUID" IS NULL
                LIMIT 5000
            ';

            $q = $this->getComponent()->getEm()->createNativeQuery($sql, new ResultSetMapping());
            $q->setParameter('AOLEVEL1', FiasComponent::AOLEVEL_1_REGION);
            $q->setParameter('AOLEVEL4', FiasComponent::AOLEVEL_4_CITY);
            $q->setParameter('AOLEVEL6', FiasComponent::AOLEVEL_6_LOCALITY);
            $q->setParameter('is_processed', GeoTemp::IS_PROCESSED_NO);

            $items = $q->getResult('ListAssocHydrator');

            $this->getComponent()->getOutput()->writeln('Cities recieved: ' . count($items) . $this->getComponent()->showMemUsage());

            if (empty($items)) {
                break;
            }

            $g = 0;
            foreach ($items as $item) {
                $this->getComponent()->setIsProcessed($item['id'], true);

                $maxId++;

                $model = new GeoCity();
                $model->setId($maxId);
                $model->setGeoRegionId($item['geo_region_id']);
                $model->setName($item['OFFNAME']);
                $model->setIsCentral(!empty($item['CENTSTATUS']));
                $model->setAOGUID($item['AOGUID']);
                $model->setUnit($item['SHORTNAME']);

                try {
                    $this->getComponent()->getEm()->persist($model);
                } catch (\Exception $e) {
                    $model->setName($item['OFFNAME'] . ' ('.$item['SHORTNAME'].')');

                    $this->getComponent()->getEm()->persist($model);
                }

                if ((++$g % 1000) == 0) {
                    $this->getComponent()->getOutput()->writeln('Cities added count from batch: ' . $g . $this->getComponent()->showMemUsage());
                }

                $added++;
            }

            $this->getComponent()->getEm()->flush();
        }

        $this->getComponent()->getOutput()->writeln('Cities added: ' . $added);
    }

    private function _processStreetTable() : void
    {
        try {
            $time = time();
            $this->getComponent()->getOutput()->writeln('===========================');
            $this->getComponent()->getOutput()->writeln('Process streets...');

            $missed = $streets = $count = 0;
            while (true) { // 1061824
                $sql = '
                    SELECT
                        gc.id AS geo_city_id,
                        gt.id,
                        gt."AOGUID",
                        gt."OFFNAME",
                        gt."SHORTNAME" 
                    FROM
                        geo_temp gt
                        INNER JOIN geo_temp gtp ON gtp."AOGUID" = gt."PARENTGUID"
                        LEFT JOIN geo_temp gtpp ON gtpp."AOGUID" = gtp."PARENTGUID"
                        LEFT JOIN geo_temp gtppp ON gtppp."AOGUID" = gtpp."PARENTGUID"
                        INNER JOIN geo_city gc ON gc."AOGUID" = CASE
                            WHEN gtp."AOLEVEL" IN ( :AOLEVEL4::INTEGER, :AOLEVEL6::INTEGER ) THEN gtp."AOGUID" 
                            WHEN gtpp."AOLEVEL" IN ( :AOLEVEL4::INTEGER, :AOLEVEL6::INTEGER ) THEN gtpp."AOGUID" 
                            WHEN gtppp."AOLEVEL" IN ( :AOLEVEL4::INTEGER, :AOLEVEL6::INTEGER ) THEN gtppp."AOGUID" 
                        END 
                    WHERE
                        gt."AOLEVEL" = :AOLEVEL7 
                        AND gt.is_processed = :is_processed
                    LIMIT 5000
                ';

                $q = $this->getComponent()->getEm()->createNativeQuery($sql, new ResultSetMapping());
                $q->setParameter('AOLEVEL4', FiasComponent::AOLEVEL_4_CITY);
                $q->setParameter('AOLEVEL6', FiasComponent::AOLEVEL_6_LOCALITY);
                $q->setParameter('AOLEVEL7', FiasComponent::AOLEVEL_7_STREET);
                $q->setParameter('is_processed', GeoTemp::IS_PROCESSED_NO);

                $items = $q->getResult('ListAssocHydrator');

                $this->getComponent()->getOutput()->writeln('Streets recieved: ' . count($items) . $this->getComponent()->showMemUsage());

                if (empty($items)) {
                    break;
                }

                $values = [];
                foreach ($items as $item) {
                    $this->getComponent()->setIsProcessed($item['id'], true);

                    $isFound = false;
                    if (!empty($item['geo_city_id'])) {
                        $isFound = true;
                    }

                    if ((++$count % 1000) == 0) {
                        $this->getComponent()->getOutput()->writeln('Streets processed count: ' . $count . $this->getComponent()->showMemUsage());
                    }

                    if (!$isFound) {
                        $missed++;

                        continue;
                    }

                    $values[] = sprintf("(%u::INTEGER, '%s', '%s', '%s')", $item['geo_city_id'], $item['AOGUID'], $item['OFFNAME'], $item['SHORTNAME']);

//                    $model = new GeoStreet();
//                    $model->setName($item['OFFNAME']);
//                    $model->setGeoCityId($item['geo_city_id']);
//                    $model->setAOGUID($item['AOGUID']);
//                    $model->setUnit($item['SHORTNAME']);
//
//                    try {
//                        $this->getComponent()->getEm()->persist($model);
//                    } catch (\Exception $e) {
//                        $model->setName($item['OFFNAME'] . ' (' . $item['SHORTNAME'] . ')');
//
//                        $this->getComponent()->getEm()->persist($model);
//                    }

                    $streets++;
                }

                $sql = '
                    WITH DATA ( geo_city_id, aoguid, name, unit ) AS 
                    ( VALUES '.implode(',', $values).')
                    INSERT INTO "geo_street" ( geo_city_id, "AOGUID", name, unit ) 
                    SELECT
                        geo_city_id::INTEGER,
                        aoguid::TEXT,
                        name::TEXT,
                        unit::TEXT
                    FROM
                        DATA 
                ';
                $statement = $this->getComponent()->getEm()->getConnection()->prepare($sql);
                $statement->execute();

//                $this->getComponent()->getEm()->flush();
            }

            $this->getComponent()->getOutput()->writeln('Streets added: ' . $streets);
            $this->getComponent()->getOutput()->writeln('Streets missed: ' . $missed);

            $this->getComponent()->getOutput()->writeln('Time remained: ' . (time() - $time) . ' sec.' . $this->getComponent()->showMemUsage());
        } catch (\Exception $e) {
            $this->getComponent()->getOutput()->writeln('Error: ' . $e->getMessage() . $e->getLine());
        }
    }
}
