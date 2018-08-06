<?php

namespace ThirdPartyBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\{InputArgument, InputInterface, InputOption};
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManager;
use XBase\{Table, Record};
use ThirdPartyBundle\Entity\{GeoRegion, GeoArea, GeoCity, GeoStreet, GeoAddress};

class KladrImportCommand extends ContainerAwareCommand
{
    const DEFAULT_KLADR_SOURCE = 'http://www.gnivc.ru/html/gnivcsoft/KLADR/Base.7z';
    const UNARCHIVE_CMD = 'p7zip x %s -o%s';

    const KLADR_ALTNAMES = 'altnames';
    const KLADR_DOMA = 'doma';
    const KLADR_KLADR = 'kladr';
    const KLADR_SOCRBASE = 'socrbase';
    const KLADR_STREET = 'street';

    const KLADR_LEVEL_1 = 1;
    const KLADR_LEVEL_2 = 2;
    const KLADR_LEVEL_3 = 3;
    const KLADR_LEVEL_4 = 4;
    const KLADR_LEVEL_5 = 5;
    const KLADR_LEVEL_6 = 6;

    /**
     * Output Interface
     *
     * @var OutputInterface
     */
    private $output;

    /**
     * Entity Manager
     *
     * @var EntityManager
     */
    private $em;

    protected function configure() : void
    {
        $this->setName('kladr:import')->setDescription('Download and update kladr database');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : void
    {
        $time = time();
        $this->output = $output;
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');

        try {
//            $archive = $this->_downloadArchive();
//            $output->writeln('Archive downloaded to : ' .$archive);
            $files = $this->_unpackArchive('$archive');
            print_r($files);
            $this->_importTables($files);
        } catch (\Exception $e) {
            $output->writeln('ERROR: ' . $e->getMessage());
        }

        $output->writeln('Time remained: ' . (time() - $time) . ' sec');
    }

    /**
     * Download kladr source archive
     *
     * @return string Downloaded file path
     *
     * @throws \Exception
     */
    private function _downloadArchive() : string
    {
        $source = self::DEFAULT_KLADR_SOURCE;
        $destination = tempnam(sys_get_temp_dir(), 'kladr_') . '.7z';
        $file_handler = fopen($destination, 'w+');

        // Download file
        $source_content = file_get_contents($source);
        if (!$source_content) {
            throw new \Exception('Could not read source file: ' . $source);
        }

        // Write file to disk
        $write_result = fwrite($file_handler, $source_content);
        if (!$write_result) {
            throw new \Exception('Could not write file: ' . $destination);
        }

        // Close the file
        fclose($file_handler);

        return $destination;
    }

    /**
     * Extract files from 7z archive
     *
     * @param string $archive
     *
     * @return array
     * @throws \Exception
     */
    private function _unpackArchive(string $archive) : array
    {
        $extractedFiles = [];

        $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'kladr_' . date('dmYHi') . DIRECTORY_SEPARATOR;
        $dir = 'c:\Temp\kladr_140920171609\\';

//        if (!is_dir($dir)) {
//            if (false === @mkdir($dir, 0777, true) && !is_dir($dir)) {
//                throw new \Exception(sprintf("Unable to create the directory: %s", $dir));
//            }
//        } elseif (!is_writable($dir)) {
//            throw new \Exception(sprintf("Unable to write in the directory: %s", $dir));
//        }
//
//        $returnVar = 0;
//        exec(
//            sprintf(self::UNARCHIVE_CMD, $archive, $dir),
//            $output,
//            $returnVar
//        );
//
//        if ($returnVar != 0) {
//            throw new \Exception("Unable to extract archive: $archive");
//        } else {
//            $this->output->writeln('Archive extracted to '.$dir);
//        }

        if ($handle = opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
                if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) === 'dbf') {
                    $entityName = strtolower(basename($file, '.DBF'));
                    $extractedFiles[$entityName] = $dir . $file;
                }
            }

            closedir($handle);
        }

        if (count($extractedFiles) === 0) {
            throw new \Exception("There is no files in directory: $dir");
        }

        return $extractedFiles;
    }

    /**
     * Import DBF files into database
     *
     * @param array $fileNames
     */
    private function _importTables(array $fileNames)
    {
        $kladr = [];
        if (!empty($fileNames[self::KLADR_KLADR])) {
            try {
                $kladr = $this->_importKladrDbf($fileNames[self::KLADR_KLADR]);
                print_r($kladr);
            } catch (\Doctrine\Common\Persistence\Mapping\MappingException $e) {
                $this->output->writeln('Skipping table: ' . $fileNames[self::KLADR_KLADR]);
                unlink($fileNames[self::KLADR_KLADR]);
            }
        }
    }

    /**
     * Process KLADR.DBF (1-4 levels)
     *
     * @param string $fileName
     *
     * @return array
     */
    private function _importKladrDbf(string $fileName) : array
    {
        $list = [];
        $this->output->writeln('Entity: ' . self::KLADR_KLADR . ', File: ' . $fileName);

        $table = new Table($fileName, null, 'cp866');
        $columns = $table->getColumns();
        foreach ($columns as $column) {
            echo $column->name.PHP_EOL;
        }

//        return $list;

        $i = 1;
        /**
         * @var $record Record
         */
        while ($record = $table->nextRecord()) {
            if ($i++ > 15) {
                break;
            }

//            print_r($record);

            $name = trim($record->getString('name'));
            $code = trim($record->getString('code'));
            $status = trim($record->getString('status'));
            $index = trim($record->getString('index'));

            if(substr($code, -2) !== '00') {
                continue;
            }

            $code = substr($code, 0, -2);

            // регион – район – город – населенный пункт
            if(substr($code, 8) !== '000') {
                $level = self::KLADR_LEVEL_4;
            } elseif(substr($code, 5, 3) !== '000') {
                $level = self::KLADR_LEVEL_3;
            } elseif(substr($code, 2, 3) !== '000') {
                $level = self::KLADR_LEVEL_2;
            } else {
                $level = self::KLADR_LEVEL_1;
            }

            $parentCode = substr($code, 0, 0 - 3 * (5 - $level));
            $parentCode = strlen($parentCode) ? str_pad($parentCode, 11, '0', STR_PAD_RIGHT) : '';

            if ($level != self::KLADR_LEVEL_2) {
                $list[$code] = ['id' => 0, 'name' => $name, 'level' => $level, 'status' => $status, 'index' => $index, 'parentCode' => $parentCode,];
            }
        }

//        // регион
//        foreach ($list as $code => &$item) {
//            if ($item['level'] === self::KLADR_LEVEL_1) {
//                $model = new GeoRegion();
//                $model->setName($item['name']);
//
//                $this->em->persist($model);
//                $this->em->flush();
//
//                $item['id'] = $model->getId();
//            }
//        }
//
//        // город
//        foreach ($list as $code => &$item) {
//            if ($item['level'] === self::KLADR_LEVEL_3) {
//                $parent = $list[$item['parentCode']] ?? [];
//
//                if (!empty($parent['id'])) {
//                    $model = new GeoCity();
//                    $model->setName($item['name']);
//                    $model->setGeoRegionId($parent['id']);
//                    $model->setIsCentral(!empty($item['status']));
//
//                    $this->em->persist($model);
//                    $this->em->flush();
//
//                    $item['id'] = $model->getId();
//                }
//            }
//        }

        // район
        foreach ($list as $code => &$item) {
            if ($item['level'] === self::KLADR_LEVEL_2) {
                print_r($item);
//                $parent = $list[$item['parentCode']] ?? [];

//                $model = new GeoArea();
//                $model->setName($item['name']);
            }
        }

        // населенный пункт
        foreach ($list as $code => &$item) {
            if ($item['level'] === self::KLADR_LEVEL_4) {

            }
        }

        return $list;
    }

    /**
     * Truncate table
     *
     * @param string $entity
     *
     * @return boolean
     */
    private function _truncateTable(string $entity) : bool
    {
        $table = $this->em->getClassMetadata($entity)->getTableName();
        $sql = 'TRUNCATE TABLE ' . $table;
        $stmt = $this->em->getConnection()->prepare($sql);

        return $stmt->execute();
    }
}