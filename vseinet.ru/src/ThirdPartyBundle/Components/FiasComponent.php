<?php

namespace ThirdPartyBundle\Components;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Output\OutputInterface;
use ThirdPartyBundle\Entity\{GeoRegion, GeoArea, GeoCity, GeoStreet, GeoTemp};

class FiasComponent
{
    const TABLE_ADDROBJ = 'ADDROBJ';
    const TABLE_HOUSE = 'HOUSE';
    const TABLE_ROOM = 'ROOM';
    const TABLE_SOCRBASE = 'SOCRBASE';

    const AOLEVEL_1_REGION = 1; // уровень региона
    const AOLEVEL_2_AR = 2; // уровень автономного округа (устаревшее)
    const AOLEVEL_3_AREA = 3; // уровень района
    const AOLEVEL_35_CL = 35; // уровень городских и сельских поселений
    const AOLEVEL_4_CITY = 4; // уровень города
    const AOLEVEL_5_IT = 5; // уровень внутригородской территории (устаревшее)
    const AOLEVEL_6_LOCALITY = 6; // уровень населенного пункта
    const AOLEVEL_65_PS = 65; // планировочная структура - ненадо
    const AOLEVEL_7_STREET = 7; // уровень улицы

    const IMPORT_DIR = '/home/dev/fias/';

    const UPDATE_ARCHIVE = 'http://fias.nalog.ru/Public/Downloads/Actual/fias_delta_xml.rar';
    const IP_GEO_BASE_UPDATE_ARCHIVE = 'http://ipgeobase.ru/files/db/Main/geo_files.zip';

    const IP_GEO_BASE_CITIES = 'cities.txt';
    const IP_GEO_BASE_CIDR = 'cidr_optim.txt';

    const AS_ADDROBJ = 'AS_ADDROBJ';

    public $fields = [
        self::TABLE_ADDROBJ => [
            'AOGUID',
            'PARENTGUID',
            'OFFNAME',
            'SHORTNAME',
            'AOLEVEL',
            'ACTSTATUS',
            'CENTSTATUS',
        ],
    ];

    public $files = [
        self::TABLE_ADDROBJ => 'AS_'.self::TABLE_ADDROBJ.'_20180329_2e6bb917-fcb4-45e8-944f-036f2fd6c8f1.XML',
    ];

    public $fileSizes = [
        self::TABLE_ADDROBJ => 2603071609,
    ];

    /**
     * FiasComponents constructor.
     *
     * @param OutputInterface $output
     * @param EntityManager   $em
     */
    public function __construct(OutputInterface $output, EntityManager $em)
    {
        $this->_output = $output;
        $this->_em = $em;
    }

    /**
     * Output Interface
     *
     * @var OutputInterface
     */
    private $_output;

    /**
     * @return OutputInterface
     */
    public function getOutput(): OutputInterface
    {
        return $this->_output;
    }

    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output)
    {
        $this->_output = $output;
    }

    /**
     * Entity Manager
     *
     * @var EntityManager
     */
    private $_em;

    /**
     * @param EntityManager $em
     */
    public function setEm(EntityManager $em)
    {
        $this->_em = $em;
    }

    /**
     * @return EntityManager
     */
    public function getEm() : EntityManager
    {
        return $this->_em;
    }

    /**
     * @param int  $id
     * @param bool $process
     *
     * @return bool
     */
    public function setIsProcessed(int $id, $process = false) : bool
    {
        if (!$process) {
            return true;
        }

        $name = $this->getEm()->getClassMetadata(GeoTemp::class)->getTableName();
        $stmt = $this->getEm()->getConnection()->prepare('UPDATE ' . $name . ' SET is_processed = ' . GeoTemp::IS_PROCESSED_YES . ' WHERE id = ' . $id);

        return $stmt->execute();
    }

    /**
     * @return string
     */
    public function showMemUsage() : string
    {
        return ' | mem(' . number_format(memory_get_peak_usage() / pow(1024, 2), 2) . ' Mb)';
    }

    /**
     * @param string $tableClass
     */
    public function truncateGeoTable(string $tableClass) : void
    {
        $name = $this->getEm()->getClassMetadata($tableClass)->getTableName();
        $sql = "TRUNCATE TABLE " . $name;
        $stmt = $this->getEm()->getConnection()->prepare($sql);

        if ($stmt->execute()) {
            $this->getOutput()->writeln('Table ' . $name . ' truncated');
        } else {
            $this->getOutput()->writeln('Table ' . $name . ' NOT truncated');
        }
    }
}