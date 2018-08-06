<?php

namespace ThirdPartyBundle\Command;

use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ThirdPartyBundle\Components\FiasComponent;
use ThirdPartyBundle\Entity\{GeoRegion, GeoCity, GeoStreet, GeoTemp};

class FiasUpdateCommand extends ContainerAwareCommand
{
    /**
     * @var FiasComponent $_component
     */
    private $_component;
    private $_parser;
    private $_currentRow = 0;
    private $_treeDepth = 0;
    private $_queue = [];
    private $_chunkSize = 409600;
    private $_defaultRow;

    private $_isDebug = false;

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
        $this->setName('fias:update')->setDescription('Update data from archive');

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

        $this->setComponent((new FiasComponent($output, $this->getContainer()->get('doctrine.orm.entity_manager'))));

        try {
            $archive = $this->downloadArchive();
            $fileInfo = $this->unpackArchive($archive);

//            $fileInfo = ['name' => 'C:\Temp\AS_ADDROBJ_20171009_8faf2701-e5dd-4831-8e55-463dd691f261.XML', 'size' => 181276577,];

            if (!empty($fileInfo)) {
                $this->processUpdate($fileInfo['name'], $fileInfo['size']);
            }
        } catch (\Exception $e) {
            $this->getComponent()->getOutput()->writeln('ERROR: ' . $e->getMessage());
        }

        $this->getComponent()->getOutput()->writeln('Time remained: ' . (time() - $time) . ' sec');
    }

    /**
     * Download fias update archive
     *
     * @return string Downloaded file path
     *
     * @throws \Exception
     */
    protected function downloadArchive() : string
    {
        $this->getComponent()->getOutput()->writeln('Download archive: '.FiasComponent::UPDATE_ARCHIVE);

        $destination = tempnam(sys_get_temp_dir(), 'fias_update_') . '.rar';

        $writed = file_put_contents($destination, fopen(FiasComponent::UPDATE_ARCHIVE, 'r'));

        if ($writed === false) {
            throw new \Exception('Could not write file: '.$destination);
        }

        return $destination;
    }

    /**
     * Extract files from rar archive
     *
     * @param string $archive
     *
     * @return array An array with files names extracted
     *
     * @throws \Exception
     */
    protected function unpackArchive(string $archive) : array
    {
        $this->getComponent()->getOutput()->writeln('Unpacking archive: '.$archive);
        $return = [];

        $rar_file = \RarArchive::open($archive);
        if ($rar_file === false) {
            throw new \Exception('Failed opening file: '.$archive);
        }

        $entries = $rar_file->getEntries();
        if ($entries === false) {
            throw new \Exception('Failed fetching entries');
        }

        $this->getComponent()->getOutput()->writeln('Found ' . count($entries) . ' files in archive');

        foreach ($entries as $entry) {
            $pos = strpos($entry->getName(), FiasComponent::AS_ADDROBJ);
            if ($pos !== false) {
                $this->getComponent()->getOutput()->writeln('File name: ' . $entry->getName());
                $this->getComponent()->getOutput()->writeln('Packed size: ' . $entry->getPackedSize().' bytes');
                $this->getComponent()->getOutput()->writeln('Unpacked size: ' . $entry->getUnpackedSize().' bytes');
                $entry->extract(sys_get_temp_dir());

                $return = ['name' => sys_get_temp_dir() . DIRECTORY_SEPARATOR . $entry->getName(), 'size' => $entry->getUnpackedSize(),];

                $this->getComponent()->getOutput()->writeln('Archive file extracted to ' . $return['name']);
            }
        }

        $rar_file->close();
        unlink($archive);

        return $return;
    }

    /**
     * @param string $file
     * @param int    $contentLength
     */
    protected function processUpdate(string $file, int $contentLength) : void
    {
        $this->getComponent()->getOutput()->writeln('Processing '.$file);

        $rows = [];
        $tableName = FiasComponent::TABLE_ADDROBJ;
        $sourceFile = fopen($file, 'r');

        $this->getComponent()->getOutput()->writeln('Filesize: '.$contentLength.' bytes');
        $processedLength = 0;
        $this->_defaultRow = array_fill_keys($this->getComponent()->fields[$tableName], null);

//        $this->getComponent()->truncateGeoTable(GeoTemp::class);

        // Парсим фрагменты файла
        $processedPercent = 0;
        while ($data = fread($sourceFile, $this->_chunkSize)) {
            xml_parse($this->_parser, $data, feof($sourceFile));
            unset($data);

            // Форматирование строки
            $this->_queue = array_reverse($this->_queue);
            while (count($this->_queue) !== 0) {
                $tableRow = array_pop($this->_queue);
                $tableRow = array_intersect_key($tableRow + $this->_defaultRow, $this->_defaultRow);

                if (!empty($tableRow['ACTSTATUS'])) {
                    $rows[$tableRow['AOGUID']] = $tableRow;
                }
            }

            if ($contentLength > 0) {
                // Вывод прогресса в консоль
                $processedLength += $this->_chunkSize;
                $currentPercent = ($processedLength < $contentLength) ? ceil($processedLength / $contentLength * 100) : 100;
                if ($currentPercent > $processedPercent) {
                    $this->getComponent()->getOutput()->writeln(PHP_EOL.'%%%%%%%%%%%%%%%%%%%%% Percent processed:  ' . $currentPercent . '%.' . $this->getComponent()->showMemUsage());
                }
                $processedPercent = $currentPercent;
            }
        }

        fclose($sourceFile);
        xml_parser_free($this->_parser);

        $this->getComponent()->getOutput()->writeln('Total rows from update file: ' . count($rows) . $this->getComponent()->showMemUsage());

        try {
            $this->_updateRegions($rows);
            $this->_updateCities($rows);
            $this->_updateStreets($rows);
        } catch (\Exception $e) {
            $this->getComponent()->getOutput()->writeln('ERROR: ' . $e->getMessage());
        }
    }

    /**
     * @param array $rows
     */
    private function _updateRegions(array &$rows) : void
    {
        $this->getComponent()->getOutput()->writeln('Update regions...' . $this->getComponent()->showMemUsage());
        $repository = $this->getComponent()->getEm()->getRepository('ThirdPartyBundle:GeoRegion');

        $regions = [];
        $items = $repository->findAll();

        /**
         * @var GeoRegion[] $items
         */
        foreach ($items as $item) {
            $regions[$item->getAOGUID()] = $item;
        }

        unset($items);

        $updated = 0;
        foreach ($rows as $AOGUID => $row) {
            $level = (int) $row['AOLEVEL'];
            if ($level === FiasComponent::AOLEVEL_1_REGION) {
                $this->_saveGeoTemp($row);

                if (isset($regions[$row['AOGUID']])) {
                    /**
                     * @var GeoRegion $region
                     */
                    $region = $regions[$row['AOGUID']];
                    if ($region->getName() !== $row['OFFNAME']) {
                        $region->setName($row['OFFNAME']);

                        if (!$this->_isDebug) {
                            $this->getComponent()->getEm()->persist($region);
                        }
                        $updated++;
                    }
                } else {
                    try {
                        $model = new GeoRegion();
                        $model->setName($row['OFFNAME']);
                        $model->setAOGUID($row['AOGUID']);
                        $model->setUnit($row['SHORTNAME']);

                        if (!$this->_isDebug) {
                            $this->getComponent()->getEm()->persist($model);
                        }

                        $updated++;
                    } catch (\Exception $e) {
                        $this->getComponent()->getOutput()->writeln('Region ' . $row['OFFNAME'] . ' (' . $row['AOGUID'] . ') NOT saved');
                    }
                }

                unset($rows[$AOGUID]);
            }
        }

        $this->getComponent()->getEm()->flush();

        $this->getComponent()->getOutput()->writeln('Added/Update regions count: ' . $updated . $this->getComponent()->showMemUsage());
    }

    /**
     * @param array $rows
     *
     */
    private function _updateCities(array &$rows) : void
    {
        $this->getComponent()->getOutput()->writeln('Update cities...' . $this->getComponent()->showMemUsage());

        // Cities
        $cities = [];
        $repository = $this->getComponent()->getEm()->getRepository('ThirdPartyBundle:GeoCity');

        $items = $repository->findAll();

        /**
         * @var GeoCity[] $items
         */
        foreach ($items as $item) {
            $cities[$item->getAOGUID()] = $item;
        }

        unset($items);

        $this->getComponent()->getOutput()->writeln('DB cities count: ' . count($cities));

        $miss = $updated = $index = $regionNotFound = 0;
        foreach ($rows as $AOGUID => $row) {
            $time = time();
            if ((++$index % 1000) == 0) {
                $this->getComponent()->getOutput()->writeln('Processed rows: ' . $index . ' of ' . count($rows) . $this->getComponent()->showMemUsage());
            }

            $level = (int)$row['AOLEVEL'];

            if ($level === FiasComponent::AOLEVEL_4_CITY || $level == FiasComponent::AOLEVEL_6_LOCALITY) {
                $this->_saveGeoTemp($row);

                $sql = '
                    SELECT
                        gr.id AS geo_region_id
                    FROM
                        geo_temp gt
                        INNER JOIN geo_temp gtp ON gtp."AOGUID" = gt."PARENTGUID"
                        LEFT JOIN geo_temp gtpp ON gtpp."AOGUID" = gtp."PARENTGUID"
                        LEFT JOIN geo_temp gtppp ON gtppp."AOGUID" = gtpp."PARENTGUID"
                        INNER JOIN geo_region gr ON gr."AOGUID" = CASE
                            WHEN gtp."AOLEVEL" = :AOLEVEL1::INTEGER THEN	gtp."AOGUID" 
                            WHEN gtpp."AOLEVEL" = :AOLEVEL1::INTEGER THEN	gtpp."AOGUID" 
                            WHEN gtppp."AOLEVEL" = :AOLEVEL1::INTEGER THEN	gtppp."AOGUID" 
                        END 
                    WHERE
                        gt."AOGUID" = :PARENTGUID
                ';

                $q = $this->getComponent()->getEm()->createNativeQuery($sql, new ResultSetMapping());
                $q->setParameter('AOLEVEL1', FiasComponent::AOLEVEL_1_REGION);
                $q->setParameter('PARENTGUID', $row['PARENTGUID']);

                $items = $q->getResult('ListAssocHydrator');
                $item = array_shift($items);

                $isCentral = !empty($row['CENTSTATUS']);
                $geoRegionId = !empty($item['geo_region_id']) ? intval($item['geo_region_id']) : 0;

                if (isset($cities[$row['AOGUID']])) {
                    $isChanged = false;
                    /**
                     * @var GeoCity $city
                     */
                    $city = $cities[$row['AOGUID']];
                    if ($city->getName() !== $row['OFFNAME']) {
                        $city->setName($row['OFFNAME']);
                        $isChanged = true;
                    }
                    if ($city->getUnit() !== $row['SHORTNAME']) {
                        $city->setUnit($row['SHORTNAME']);
                        $isChanged = true;
                    }
                    if ($city->getIsCentral() != $isCentral) {
                        $city->setIsCentral($isCentral);
                        $isChanged = true;
                    }
                    if ($city->getGeoRegionId() != $geoRegionId) {
                        $city->setGeoRegionId($geoRegionId);
                        $isChanged = true;
                    }

                    if ($isChanged) {
                        $this->getComponent()->getEm()->persist($city);
                        $this->getComponent()->getEm()->flush();

                        $updated++;
                    }
                } else {
                    if (!empty($geoRegionId)) {
                        $model = new GeoCity();
                        $model->setName($row['OFFNAME']);
                        $model->setAOGUID($row['AOGUID']);
                        $model->setGeoRegionId($geoRegionId);
                        $model->setIsCentral($isCentral);
                        $model->setUnit($row['SHORTNAME']);

                        try {
                            $this->getComponent()->getEm()->persist($model);
                            $this->getComponent()->getEm()->flush();

                            $this->getComponent()->getOutput()->writeln('Added city ' . $row['OFFNAME']);
                        } catch (\Exception $e) {
                            $model->setName($row['OFFNAME'] . ' (' . $row['SHORTNAME'] . ')');

                            $this->getComponent()->getEm()->persist($model);
                            $this->getComponent()->getEm()->flush();
                        }

                        $updated++;
                    } else {
                        $this->getComponent()->getOutput()->writeln('Unable to determine geo_rubric_id for new city '.$row['OFFNAME'].', PARENTGUID['.$row['PARENTGUID'].']. Skipped...');
                        $regionNotFound++;
                    }
                }

                unset($rows[$AOGUID]);
            }

            $this->getComponent()->getOutput()->writeln('Row ['.$index.'] time remained: ' . (time() - $time) . ' sec.' . $this->getComponent()->showMemUsage());
        }

        $this->getComponent()->getOutput()->writeln('Added/Update cities count: ' . $updated . $this->getComponent()->showMemUsage());
        $this->getComponent()->getOutput()->writeln('Skipped cities count: ' . $miss . $this->getComponent()->showMemUsage());
        $this->getComponent()->getOutput()->writeln('Region not found: ' . $regionNotFound . $this->getComponent()->showMemUsage());
    }

    /**
     * @param array $rows
     */
    private function _updateStreets(array &$rows) : void
    {
        $this->getComponent()->getOutput()->writeln('Update streets...' . $this->getComponent()->showMemUsage().PHP_EOL);

        $updated = $index = $cityNotFound = 0;
        foreach ($rows as $AOGUID => $row) {
            if ((++$index % 10000) == 0) {
                $this->getComponent()->getOutput()->writeln('Processed rows: ' . $index . ' of ' . count($rows) . $this->getComponent()->showMemUsage());
            }

            $level = (int)$row['AOLEVEL'];

            if ($level === FiasComponent::AOLEVEL_7_STREET) {
                $this->_saveGeoTemp($row);

                $street = $this->getComponent()->getEm()->getRepository('ThirdPartyBundle:GeoStreet')->findOneBy(['AOGUID' => $row['AOGUID'],]);

                $sql = '
                    SELECT
                        gr.id AS geo_city_id
                    FROM
                        geo_temp gt
                        INNER JOIN geo_temp gtp ON gtp."AOGUID" = gt."PARENTGUID"
                        LEFT JOIN geo_temp gtpp ON gtpp."AOGUID" = gtp."PARENTGUID"
                        LEFT JOIN geo_temp gtppp ON gtppp."AOGUID" = gtpp."PARENTGUID"
                        INNER JOIN geo_region gr ON gr."AOGUID" = CASE
                            WHEN gtp."AOLEVEL" IN ( :AOLEVEL4::INTEGER, :AOLEVEL6::INTEGER ) THEN	gtp."AOGUID" 
                            WHEN gtpp."AOLEVEL" IN ( :AOLEVEL4::INTEGER, :AOLEVEL6::INTEGER ) THEN	gtpp."AOGUID" 
                            WHEN gtppp."AOLEVEL" IN ( :AOLEVEL4::INTEGER, :AOLEVEL6::INTEGER ) THEN	gtppp."AOGUID" 
                        END 
                    WHERE
                        gt."AOGUID" = :PARENTGUID
                ';

                $q = $this->getComponent()->getEm()->createNativeQuery($sql, new ResultSetMapping());
                $q->setParameter('AOLEVEL4', FiasComponent::AOLEVEL_4_CITY);
                $q->setParameter('AOLEVEL6', FiasComponent::AOLEVEL_6_LOCALITY);
                $q->setParameter('PARENTGUID', $row['PARENTGUID']);

                $items = $q->getResult('ListAssocHydrator');
                $item = array_shift($items);

                $cityID = !empty($item['geo_city_id']) ? intval($item['geo_city_id']) : 0;

                if ($street) {
                    $isChanged = false;
                    if ($street->getName() !== $row['OFFNAME']) {
                        $street->setName($row['OFFNAME']);
                        $isChanged = true;
                    }
                    if (!empty($cityID) && $street->getGeoCityId() != $cityID) {
                        $street->setGeoCityId($cityID);
                        $isChanged = true;
                    }
                    if ($street->getUnit() != $row['SHORTNAME']) {
                        $street->setUnit($row['SHORTNAME']);
                        $isChanged = true;
                    }

                    if ($isChanged) {
                        $this->getComponent()->getEm()->persist($street);
                        $this->getComponent()->getEm()->flush();
                        $updated++;
                    }
                } else {
                    if ($cityID) {
                        $model = new GeoStreet();
                        $model->setName($row['OFFNAME']);
                        $model->setAOGUID($row['AOGUID']);
                        $model->setGeoCityId($cityID);
                        $model->setUnit($row['SHORTNAME']);

                        try {
                            $this->getComponent()->getEm()->persist($model);
                            $this->getComponent()->getEm()->flush();

                        } catch (\Exception $e) {
                            $model->setName($row['OFFNAME'] . ' (' . $row['SHORTNAME'] . ')');

                            $this->getComponent()->getEm()->persist($model);
                            $this->getComponent()->getEm()->flush();
                        }

                        $updated++;
                    } else {
                        $this->getComponent()->getOutput()->writeln('Unable to determine geo_city_id for new street '.$row['OFFNAME'].', PARENTGUID['.$row['PARENTGUID'].']. Skipped...');
                        $cityNotFound++;
                    }
                }
            }
        }

        $this->getComponent()->getOutput()->writeln('Added/Update streets count: ' . $updated);
        $this->getComponent()->getOutput()->writeln('City not found: ' . $cityNotFound . $this->getComponent()->showMemUsage());
    }

    /**
     * @param array $row
     */
    private function _saveGeoTemp(array $row) : void
    {
        $geoTemp = $this->getComponent()->getEm()->getRepository('ThirdPartyBundle:GeoTemp')->findOneBy(['AOGUID' => $row['AOGUID'],]);

        if (!$geoTemp) {
            $geoTemp = new GeoTemp();
            $geoTemp->setAOGUID($row['AOGUID']);
            $geoTemp->setPARENTGUID($row['PARENTGUID']);
            $geoTemp->setOFFNAME($row['OFFNAME']);
            $geoTemp->setSHORTNAME($row['SHORTNAME']);
            $geoTemp->setAOLEVEL($row['AOLEVEL']);
            $geoTemp->setIsProcessed(GeoTemp::IS_PROCESSED_YES);

            $isSave = true;
        } else {
            $isSave = false;

            if ($geoTemp->getOFFNAME() !== $row['OFFNAME']) {
                $geoTemp->setOFFNAME($row['OFFNAME']);
                $isSave = true;
            }
            if ($geoTemp->getSHORTNAME() !== $row['SHORTNAME']) {
                $geoTemp->setSHORTNAME($row['SHORTNAME']);
                $isSave = true;
            }
            if ($geoTemp->getAOLEVEL() !== $row['AOLEVEL']) {
                $geoTemp->setAOLEVEL($row['AOLEVEL']);
                $isSave = true;
            }
        }

        if ($isSave) {
            $this->getComponent()->getEm()->persist($geoTemp);
            $this->getComponent()->getEm()->flush();
        }
    }
}
