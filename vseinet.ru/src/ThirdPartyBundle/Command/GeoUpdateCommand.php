<?php

namespace ThirdPartyBundle\Command;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManager;
use ThirdPartyBundle\Components\FiasComponent;
use ThirdPartyBundle\Entity\{GeoRegion, GeoArea, GeoCity, GeoStreet, GeoTemp};

class GeoUpdateCommand extends ContainerAwareCommand
{
    const TYPE_REGIONS = 'regions';
    const TYPE_CITIES = 'cities';
    const TYPE_STREETS = 'streets';

    /**
     * @var FiasComponent $_component
     */
    private $_component;

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
        $this->setName('geo:update')->setDescription('Update geo tables')
            ->addArgument('type', InputArgument::REQUIRED, 'Table type ('.self::TYPE_REGIONS.'|'.self::TYPE_CITIES.'|'.self::TYPE_STREETS.')');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : void
    {
        $time = time();
        $this->setComponent((new FiasComponent($output, $this->getContainer()->get('doctrine.orm.entity_manager'))));

        $type = strval($input->getArgument('type'));

        if (!in_array($type, [self::TYPE_REGIONS, self::TYPE_CITIES, self::TYPE_STREETS,])) {
            throw new \Exception('Неверный тип таблицы: '.$type);
        }

        try {
            switch ($type) {
                case self::TYPE_REGIONS:
                    $this->_updateRegions();
                    break;

                case self::TYPE_CITIES:
                    $this->_updateCities();
                    break;

                case self::TYPE_STREETS:
                    $this->_updateStreets();
                    break;
            }
        } catch (\Exception $e) {
            $this->getComponent()->getOutput()->writeln('ERROR: ' . $e->getMessage());
        }

        $this->getComponent()->getOutput()->writeln('Time remained: ' . (time() - $time) . ' sec');
    }

    private function _updateRegions() : void
    {
        $this->getComponent()->getOutput()->writeln('Update '.self::TYPE_REGIONS);

        $this->getComponent()->getEm()->getConnection()->beginTransaction();
        try {
            $sql = '
                UPDATE geo_region_old AS gro 
                SET "AOGUID" = gr."AOGUID", new_id = gr.id
                FROM geo_region AS gr
                WHERE gr.name LIKE \'%\' || gro.name || \'%\'
            ';
            $statement = $this->getComponent()->getEm()->getConnection()->prepare($sql);
            $statement->execute();

            $statement = $this->getComponent()->getEm()->getConnection()->prepare('UPDATE geo_region_old SET "AOGUID" = :AOGUID::TEXT, new_id = :new_id WHERE name = :name::TEXT');
            $statement->bindValue('AOGUID', '1781f74e-be4a-4697-9c6b-493057c94818', Type::STRING);
            $statement->bindValue('new_id', 86);
            $statement->bindValue('name', 'Кабардино-Балкария', Type::STRING);
            $statement->execute();

            $statement = $this->getComponent()->getEm()->getConnection()->prepare('UPDATE geo_region_old SET "AOGUID" = :AOGUID::TEXT, new_id = :new_id WHERE name = :name::TEXT');
            $statement->bindValue('AOGUID', '61b95807-388a-4cb1-9bee-889f7cf811c8', Type::STRING);
            $statement->bindValue('new_id', 84);
            $statement->bindValue('name', 'Карачаево-Черкессия', Type::STRING);
            $statement->execute();

            $statement = $this->getComponent()->getEm()->getConnection()->prepare('UPDATE geo_region_old SET "AOGUID" = :AOGUID::TEXT, new_id = :new_id WHERE name = :name::TEXT');
            $statement->bindValue('AOGUID', 'c225d3db-1db6-4063-ace0-b3fe9ea3805f', Type::STRING);
            $statement->bindValue('new_id', 79);
            $statement->bindValue('name', 'Саха (Якутия)', Type::STRING);
            $statement->execute();

            $statement = $this->getComponent()->getEm()->getConnection()->prepare('UPDATE geo_region_old SET "AOGUID" = :AOGUID::TEXT, new_id = :new_id WHERE name = :name::TEXT');
            $statement->bindValue('AOGUID', 'de459e9c-2933-4923-83d1-9c64cfd7a817', Type::STRING);
            $statement->bindValue('new_id', 78);
            $statement->bindValue('name', 'Северная Осетия (Алания)', Type::STRING);
            $statement->execute();

            $statement = $this->getComponent()->getEm()->getConnection()->prepare('UPDATE geo_region_old SET "AOGUID" = :AOGUID::TEXT, new_id = :new_id WHERE name = :name::TEXT');
            $statement->bindValue('AOGUID', '026bc56f-3731-48e9-8245-655331f596c0', Type::STRING);
            $statement->bindValue('new_id', 76);
            $statement->bindValue('name', 'Тыва (Тува)', Type::STRING);
            $statement->execute();

            $statement = $this->getComponent()->getEm()->getConnection()->prepare('UPDATE geo_region_old SET "AOGUID" = :AOGUID::TEXT, new_id = :new_id WHERE name = :name::TEXT');
            $statement->bindValue('AOGUID', '52618b9c-bcbb-47e7-8957-95c63f0b17cc', Type::STRING);
            $statement->bindValue('new_id', 75);
            $statement->bindValue('name', 'Удмуртия', Type::STRING);
            $statement->execute();

            $statement = $this->getComponent()->getEm()->getConnection()->prepare('UPDATE geo_region_old SET "AOGUID" = :AOGUID::TEXT, new_id = :new_id WHERE name = :name::TEXT');
            $statement->bindValue('AOGUID', 'de67dc49-b9ba-48a3-a4cc-c2ebfeca6c5e', Type::STRING);
            $statement->bindValue('new_id', 73);
            $statement->bindValue('name', 'Чечня', Type::STRING);
            $statement->execute();

            $statement = $this->getComponent()->getEm()->getConnection()->prepare('UPDATE geo_region_old SET "AOGUID" = :AOGUID::TEXT, new_id = :new_id WHERE name = :name::TEXT');
            $statement->bindValue('AOGUID', '878fc621-3708-46c7-a97f-5a13a4176b3e', Type::STRING);
            $statement->bindValue('new_id', 72);
            $statement->bindValue('name', 'Чувашия', Type::STRING);
            $statement->execute();

            $this->getComponent()->getEm()->getConnection()->commit();
        } catch (\Exception $ex) {
            $this->getComponent()->getEm()->getConnection()->rollback();

            throw $ex;
        }
    }

    private function _updateCities() : void
    {
        $this->getComponent()->getOutput()->writeln('Update '.self::TYPE_CITIES);

        $this->getComponent()->getEm()->getConnection()->beginTransaction();
        try {
            $sql = '
                SELECT
                    gco.id,
                    gro.new_id as geo_region_id, 
                    gco.name,
                    gco.is_central,
                    gt."AOGUID",
		            gt."SHORTNAME" AS unit
                FROM
                    geo_city_old gco
                    INNER JOIN geo_region_old gro ON gro.id = gco.geo_region_id
                    INNER JOIN geo_temp gt ON gt."OFFNAME" = gco.NAME AND gt."AOLEVEL" IN ( :AOLEVEL4::INTEGER, :AOLEVEL6::INTEGER )
                    INNER JOIN geo_temp gtr ON gtr."AOGUID" = gt."PARENTGUID"
                    LEFT JOIN geo_temp gtrr ON gtrr."AOGUID" = gtr."PARENTGUID"
                    LEFT JOIN geo_temp gtrrr ON gtrrr."AOGUID" = gtrr."PARENTGUID"
                WHERE
                    CASE
                        when gtr."AOLEVEL" = :AOLEVEL1::INTEGER THEN gtr."AOGUID"
                        when gtrr."AOLEVEL" = :AOLEVEL1::INTEGER THEN gtrr."AOGUID"
                        when gtrrr."AOLEVEL" = :AOLEVEL1::INTEGER THEN gtrrr."AOGUID"
                    END = gro."AOGUID"
                ORDER BY
                    gt."OFFNAME",
                    gt."AOLEVEL"                    
            ';
            $q = $this->getComponent()->getEm()->createNativeQuery($sql, new ResultSetMapping());
            $q->setParameter('AOLEVEL1', FiasComponent::AOLEVEL_1_REGION);
            $q->setParameter('AOLEVEL4', FiasComponent::AOLEVEL_4_CITY);
            $q->setParameter('AOLEVEL6', FiasComponent::AOLEVEL_6_LOCALITY);

            $rows = $q->getResult('ListAssocHydrator');

            $this->getComponent()->getOutput()->writeln('Cities with region: ' . count($rows) . $this->getComponent()->showMemUsage());

            $cities = [];
            foreach ($rows as $row) {
                $cities[$row['id']][] = $row;
            }

            $add = [];
            foreach ($cities as $id => $rows) {
                $add[] = array_shift($rows);
            }

            $this->getComponent()->getOutput()->writeln('Filtered cities with region: ' . count($add) . $this->getComponent()->showMemUsage());

            foreach ($add as $row) {
                $model = new GeoCity();
                $model->setId($row['id']);
                $model->setName($row['name']);
                $model->setAOGUID($row['AOGUID']);
                $model->setGeoRegionId($row['geo_region_id']);
                $model->setIsCentral($row['is_central']);
                $model->setUnit($row['unit']);

                $this->getComponent()->getEm()->persist($model);

                $this->getComponent()->getOutput()->writeln('Added city ' . $row['name']);
            }

            $this->getComponent()->getEm()->flush();


            // удаление "плохих" городов без регионов
            $sql = "
                DELETE FROM geo_city_old 
                WHERE id IN (
                    SELECT
                        gco.id 
                    FROM
                        geo_city_old gco
                        LEFT JOIN geo_temp gt ON gt.\"AOLEVEL\" IN (:AOLEVEL4, :AOLEVEL6) AND REPLACE ( REPLACE ( LOWER( gt.\"OFFNAME\" ), ' ', '-' ), 'ё', 'е' ) = REPLACE ( REPLACE ( LOWER( gco.name ), ' ', '-' ), 'ё', 'е' ) 
                    WHERE
                        gt.\"OFFNAME\" IS NULL 
                )
            ";
            $statement = $this->getComponent()->getEm()->getConnection()->prepare($sql);
            $statement->bindValue('AOLEVEL4', FiasComponent::AOLEVEL_4_CITY);
            $statement->bindValue('AOLEVEL6', FiasComponent::AOLEVEL_6_LOCALITY);
            $statement->execute();

            $sql = '
                SELECT
                    gco.id,
                    gr.id AS geo_region_id,
                    gco.name,
                    gco.is_central,
                    
                    gt."AOGUID" AS aoguid,
                    gt."SHORTNAME" AS shortname,
                    gt."AOLEVEL" AS aolevel
                FROM
                    geo_city_old gco
                    INNER JOIN geo_temp gt ON gt."OFFNAME" = gco.NAME AND gt."AOLEVEL" IN ( :AOLEVEL4::INTEGER, :AOLEVEL6::INTEGER )
                    INNER JOIN geo_temp gtr ON gtr."AOGUID" = gt."PARENTGUID"
                    LEFT JOIN geo_temp gtrr ON gtrr."AOGUID" = gtr."PARENTGUID" AND gtr."AOLEVEL" <> :AOLEVEL1::INTEGER
                    LEFT JOIN geo_temp gtrrr ON gtrrr."AOGUID" = gtrr."PARENTGUID" AND gtrr."AOLEVEL" <> :AOLEVEL1::INTEGER
                    INNER JOIN geo_region gr ON gr."AOGUID" = case
                        when gtr."AOLEVEL" = :AOLEVEL1::INTEGER THEN gtr."AOGUID"
                        when gtrr."AOLEVEL" = :AOLEVEL1::INTEGER THEN gtrr."AOGUID"
                        when gtrrr."AOLEVEL" = :AOLEVEL1::INTEGER THEN gtrrr."AOGUID"
                    END
                    LEFT JOIN geo_city AS gc ON gc.name = gco.name AND gc.geo_region_id = gr.id 
                WHERE
                    gco.geo_region_id = 0 
                    AND gc.id IS NULL 
                ORDER BY
                    gt."OFFNAME",
                    gt."AOLEVEL"
            ';

            $q = $this->getComponent()->getEm()->createNativeQuery($sql, new ResultSetMapping());
            $q->setParameter('AOLEVEL1', FiasComponent::AOLEVEL_1_REGION);
            $q->setParameter('AOLEVEL4', FiasComponent::AOLEVEL_4_CITY);
            $q->setParameter('AOLEVEL6', FiasComponent::AOLEVEL_6_LOCALITY);

            $rows = $q->getResult('ListAssocHydrator');

            $this->getComponent()->getOutput()->writeln('Cities without region: ' . count($rows) . $this->getComponent()->showMemUsage());

            $cities = [];
            foreach ($rows as $row) {
                $cities[$row['id']][] = $row;
            }

            $add = [];
            foreach ($cities as $id => $rows) {
                $add[] = array_shift($rows);
            }

            $this->getComponent()->getOutput()->writeln('Filtered cities without region: ' . count($add) . $this->getComponent()->showMemUsage());

            // Добавление 4 городов-регионов
            $add[] = [
                'id' => 4,
                'name' => 'Москва',
                'aoguid' => '0c5b2444-70a0-4932-980c-b4dc0d3f02b5',
                'geo_region_id' => 16,
                'is_central' => 't',
                'shortname' => 'г',
            ];
            $add[] = [
                'id' => 967,
                'name' => 'Севастополь',
                'aoguid' => '6fdecb78-893a-4e3f-a5ba-aa062459463b',
                'geo_region_id' => 2,
                'is_central' => 't',
                'shortname' => 'г',
            ];
            $add[] = [
                'id' => 5,
                'name' => 'Санкт-Петербург',
                'aoguid' => 'c2deb16a-0330-4f05-821f-1d09c93331e6',
                'geo_region_id' => 15,
                'is_central' => 't',
                'shortname' => 'г',
            ];
            $add[] = [
                'id' => 184,
                'name' => 'Байконур',
                'aoguid' => '63ed1a35-4be6-4564-a1ec-0c51f7383314',
                'geo_region_id' => 1,
                'is_central' => 't',
                'shortname' => 'г',
            ];


            foreach ($add as $row) {
                $model = new GeoCity();
                $model->setId($row['id']);
                $model->setName($row['name']);
                $model->setAOGUID($row['aoguid']);
                $model->setGeoRegionId($row['geo_region_id']);
                $model->setIsCentral($row['is_central']);
                $model->setUnit($row['shortname']);

                try {
                    $this->getComponent()->getEm()->persist($model);
                } catch (\Exception $e) {
                    $model->setName($row['name'] . ' ('.$row['shortname'].')');

                    $this->getComponent()->getEm()->persist($model);
                }

                $this->getComponent()->getOutput()->writeln('Added city ' . $row['name']);
            }

            $this->getComponent()->getEm()->flush();

            // set seq
            $this->getComponent()->getOutput()->writeln(PHP_EOL.'Set geo_city sequence...');
            $sql = 'select max(id) as max_id from geo_city';

            $q = $this->getComponent()->getEm()->createNativeQuery($sql, new ResultSetMapping());

            $rows = $q->getResult('ListAssocHydrator');
            $row = array_shift($rows);

            $maxId = $row['max_id'];

            $sql = "
                SELECT pg_catalog.setval('geo_city_id_seq', :id, false)
            ";

            $q = $this->getComponent()->getEm()->createNativeQuery($sql, new ResultSetMapping());
            $q->setParameter('id', $maxId);
            $q->execute();

            $this->getComponent()->getOutput()->writeln('Sequence setted to '.$maxId);

            $this->getComponent()->getEm()->getConnection()->commit();
        } catch (\Exception $ex) {
            $this->getComponent()->getEm()->getConnection()->rollback();

            throw $ex;
        }
    }

    private function _updateStreets() : void
    {
        $this->getComponent()->getOutput()->writeln('Update '.self::TYPE_STREETS);

        $this->getComponent()->getEm()->getConnection()->beginTransaction();
        try {
            $sql = '
                UPDATE geo_street AS gs 
                SET geo_city_id = gc.id, unit = :unit
                FROM
                    geo_city AS gc,
                    geo_temp AS gt 
                WHERE
                    gs."AOGUID" = gt."AOGUID" 
                    AND gc."AOGUID" = gt."PARENTGUID" 
                    AND gs."AOGUID" IS NOT NULL 
                    AND gs.geo_city_id IS NULL
            ';
            $statement = $this->getComponent()->getEm()->getConnection()->prepare($sql);
            $statement->bindValue('unit', 'ул');
//            $statement->execute();

            $sql = '
                UPDATE geo_street AS gs 
                SET geo_city_id = gc.id, unit = :unit
                FROM
                    geo_temp AS gst, 
                    geo_temp AS gat,
                    geo_temp AS gct,
                    geo_city AS gc
                WHERE
                    gs."AOGUID" = gst."AOGUID" 
                    AND gat."AOGUID" = gst."PARENTGUID" 
                    AND gct."AOGUID" = gat."PARENTGUID" 
                    AND gct."AOGUID" = gc."AOGUID"
                    AND gs."AOGUID" IS NOT NULL 
                    AND gs.geo_city_id IS NULL
            ';
            $statement = $this->getComponent()->getEm()->getConnection()->prepare($sql);
            $statement->bindValue('unit', 'ул');
//            $statement->execute();

            $this->getComponent()->getEm()->getConnection()->commit();
        } catch (\Exception $ex) {
            $this->getComponent()->getEm()->getConnection()->rollback();

            throw $ex;
        }
    }
}
