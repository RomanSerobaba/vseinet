<?php

namespace ThirdPartyBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ThirdPartyBundle\Components\FiasComponent;
use ThirdPartyBundle\Entity\GeoTemp;

class FiasImportCommand extends ContainerAwareCommand
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

    private $_contentLength;
    private $_contentLengthStep;
    private $_processedLength;
    private $_defaultRow;

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
        ini_set('memory_limit', '2048M');
        $this->setName('fias:import')->setDescription('Import FIAS database to temp table');

        $this->_parser = xml_parser_create();
        xml_parser_set_option($this->_parser, XML_OPTION_CASE_FOLDING, false);
        xml_set_object($this->_parser, $this);
        xml_set_element_handler($this->_parser, "parserOpenTag", "parserClosetag");
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

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output) : void
    {
        $time = time();

        $this->setComponent((new FiasComponent($output, $this->getContainer()->get('doctrine.orm.entity_manager'))));

        try {
            $this->_processAddresses();
        } catch (\Exception $e) {
            $this->getComponent()->getOutput()->writeln('ERROR: ' . $e->getMessage());
        }

        $this->getComponent()->getOutput()->writeln('Time remained: ' . (time() - $time) . ' sec');
    }

    /**
     * @throws \Exception
     */
    private function _processAddresses() : void
    {
        $tableName = FiasComponent::TABLE_ADDROBJ;
        $dataFile = FiasComponent::IMPORT_DIR . $this->getComponent()->files[$tableName];

        if (file_exists($dataFile) === false) {
            throw new \Exception('File not found: ' . $dataFile);
        }

         $this->getComponent()->getOutput()->writeln('Processing '.$dataFile);

        $sourceFile = fopen($dataFile, 'r');

        $this->_contentLength = $this->getComponent()->fileSizes[$tableName];
        $this->getComponent()->getOutput()->writeln('Filesize: '.$this->_contentLength);
        $this->_processedLength = 0;
        $this->_defaultRow = array_fill_keys($this->getComponent()->fields[$tableName], null);

        $this->getComponent()->truncateGeoTable(GeoTemp::class);

        // Парсим фрагменты файла
        $processedPercent = 0;
        $i = 0;
        while ($data = fread($sourceFile, $this->_chunkSize)) {
            if (($i % 50) == 0) {
                 $this->getComponent()->getOutput()->writeln('>>> XML packages processed: ' . $i . $this->getComponent()->showMemUsage());
            }
            xml_parse($this->_parser, $data, feof($sourceFile));
            unset($data);

            // Форматирование строки
            $this->_queue = array_reverse($this->_queue);
            $n = 0;
            while (count($this->_queue) !== 0) {
                $tableRow = array_pop($this->_queue);
                $tableRow = array_intersect_key($tableRow + $this->_defaultRow, $this->_defaultRow);

                if ($this->_processAddressRow($tableRow)) {
                    $n++;
                }

//                file_put_contents(self::INPUT_DIR . $tableName . time() . '.log', print_r($tableRow, 1), FILE_APPEND);
            }

//            $this->getComponent()->getEm()->flush();

            if (($i % 25) == 0) {
                 $this->getComponent()->getOutput()->writeln('DB data (' . $n . ') saved');
            }

            if ($this->_contentLength > 0) {
                // Вывод прогресса в консоль
                $this->_processedLength += $this->_chunkSize;
                $currentPercent = ($this->_processedLength < $this->_contentLength) ? ceil($this->_processedLength / $this->_contentLength * 100) : 100;
                if ($currentPercent > $processedPercent) {
                     $this->getComponent()->getOutput()->writeln(PHP_EOL.'%%%%%%%%%%%%%%%%%%%%% Percent processed:  ' . $currentPercent . '%.' . $this->getComponent()->showMemUsage());
                }
                $processedPercent = $currentPercent;
            }

            $i++;

//             if ($i >= 3000) {
//                 break;
//             }
        }

        fclose($sourceFile);
        xml_parser_free($this->_parser);
    }

    /**
     * @param array $row
     *
     * @return bool
     */
    private function _processAddressRow(array $row) : bool
    {
        if (!empty($row['ACTSTATUS'])) {
            foreach ($row as $key => &$value) {
                if (empty($value)) {
                    $value = '';
                }
            }

            $sql = sprintf("INSERT INTO %s VALUES ('%s', '%s', '%s', '%s', %u, %u, %u);",
                'geo_temp',
                $row['AOGUID'],
                $row['PARENTGUID'],
                $row['OFFNAME'],
                $row['SHORTNAME'],
                intval($row['AOLEVEL']),
                intval($row['CENTSTATUS']),
                GeoTemp::IS_PROCESSED_NO
            );

            $stmt = $this->getComponent()->getEm()->getConnection()->prepare($sql);

            return $stmt->execute();
        }

        return false;
    }
}
