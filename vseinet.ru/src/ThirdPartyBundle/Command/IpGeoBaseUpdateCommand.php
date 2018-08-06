<?php

namespace ThirdPartyBundle\Command;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ThirdPartyBundle\Components\FiasComponent;
use ThirdPartyBundle\Entity\{
    GeoIp, GeoCities
};

class IpGeoBaseUpdateCommand extends ContainerAwareCommand
{
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
        $this->setName('ipgeobase:update')->setDescription('Update IpGeoBase.ru tables');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : void
    {
        $time = time();
        $this->setComponent((new FiasComponent($output, $this->getContainer()->get('doctrine.orm.entity_manager'))));

        try {
            $archive = $this->downloadArchive();
            $this->unpackArchive($archive);
            $this->processUpdate();
            $this->fixUpdate();
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
        $this->getComponent()->getOutput()->writeln('Download archive: '.FiasComponent::IP_GEO_BASE_UPDATE_ARCHIVE);

        $pathParts = pathinfo(FiasComponent::IP_GEO_BASE_UPDATE_ARCHIVE);

        $destination = FiasComponent::IMPORT_DIR . DIRECTORY_SEPARATOR . $pathParts['basename'];

        $writed = file_put_contents($destination, fopen(FiasComponent::IP_GEO_BASE_UPDATE_ARCHIVE, 'r'));

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
     * @throws \Exception
     */
    protected function unpackArchive(string $archive) : void
    {
        $this->getComponent()->getOutput()->writeln('Unpacking archive: '.$archive);

        $zip = new \ZipArchive;
        if ($zip->open($archive) === true) {
            if ($zip->extractTo(FiasComponent::IMPORT_DIR)) {
                $zip->close();
            } else {
                throw new \Exception('Unable extract archive: '.$archive);
            }
        } else {
            throw new \Exception('Failed opening file: '.$archive);
        }
    }

    protected function processUpdate() : void
    {
        $this->getComponent()->getOutput()->writeln('Import '.GeoCities::class);
        $fileName = FiasComponent::IMPORT_DIR.DIRECTORY_SEPARATOR.FiasComponent::IP_GEO_BASE_CITIES;
        if(file_exists($fileName)) {
            $this->getComponent()->truncateGeoTable(GeoCities::class);
            $file = file($fileName);
            $pattern = '#(\d+)\s+(.*?)\t+(.*?)\t+(.*?)\t+(.*?)\s+(.*)#';
            $added = 0;
            foreach ($file as $row) {
                $row = iconv('windows-1251', 'utf-8', $row);
                if(preg_match($pattern, $row, $out)) {
                    $model = new GeoCities();
                    $model->setCityId(intval($out[1]));
                    $model->setCity($out[2]);
                    $model->setRegion($out[3]);
                    $model->setDistrict($out[4]);
                    $model->setLat(floatval($out[5]));
                    $model->setLng(floatval($out[6]));

                    $this->getComponent()->getEm()->persist($model);
                    $added++;
                }
            }

            $this->getComponent()->getEm()->flush();
            $this->getComponent()->getOutput()->writeln('Total added rows: '.$added);
        } else {
            throw new NotFoundHttpException('File '.$fileName.' not found');
        }

        $this->getComponent()->getOutput()->writeln(PHP_EOL.'Import '.GeoIp::class);
        $fileName = FiasComponent::IMPORT_DIR.DIRECTORY_SEPARATOR.FiasComponent::IP_GEO_BASE_CIDR;
        if(file_exists(FiasComponent::IMPORT_DIR.DIRECTORY_SEPARATOR.FiasComponent::IP_GEO_BASE_CIDR)) {
            $this->getComponent()->truncateGeoTable(GeoIp::class);
            $file = file($fileName);
            $pattern = '#(\d+)\s+(\d+)\s+(\d+\.\d+\.\d+\.\d+)\s+-\s+(\d+\.\d+\.\d+\.\d+)\s+(\w+)\s+(\d+|-)#';
            $added = 0;
            foreach ($file as $row) {
//                $row = iconv('windows-1251', 'utf-8', $row);
                if(preg_match($pattern, $row, $out) && $out[5] === 'RU') {
                    $sql = 'INSERT INTO "public"."geo_ip"("long_ip1", "long_ip2", "ip1", "ip2", "city_id") 
                          VALUES (:long_ip1, :long_ip2, :ip1, :ip2, :city_id)';
                    $statement = $this->getComponent()->getEm()->getConnection()->prepare($sql);
                    $statement->bindValue('long_ip1', intval($out[1]), Type::INTEGER);
                    $statement->bindValue('long_ip2', intval($out[2]), Type::INTEGER);
                    $statement->bindValue('ip1', $out[3], Type::STRING);
                    $statement->bindValue('ip2', $out[4], Type::STRING);
                    $statement->bindValue('city_id', intval($out[6]), Type::INTEGER);
                    $statement->execute();

                    if ((++$added % 1000) == 0) {
                        $this->getComponent()->getOutput()->writeln('Base added rows: ' . $added. $this->getComponent()->showMemUsage());
                    }
                }
            }

            $this->getComponent()->getOutput()->writeln('Total added rows: '.$added);
        } else {
            throw new NotFoundHttpException('File '.$fileName.' not found');
        }
    }

    protected function fixUpdate() : void
    {
        // Нормальные города
        $sql = '
            SELECT
                gcs.city_id,
                gc."id" AS geo_city_id
            FROM
                "geo_ip" gb
                INNER JOIN "__geo_cities" gcs ON gcs.city_id = gb.city_id
                INNER JOIN "geo_city" gc ON gcs.city = gc."name"
                INNER JOIN "geo_region" gr ON gr.id = gc.geo_region_id
                INNER JOIN geo_temp gt ON gc."AOGUID" = gt."AOGUID" 
            WHERE
                gt."AOLEVEL" = :AOLEVEL4::INTEGER 
                AND gc.unit = :unit::TEXT
            GROUP BY
                gcs.city_id,
                gc."id" 
            ORDER BY
                gcs.city,
                gc.unit
        ';

        $q = $this->getComponent()->getEm()->createNativeQuery($sql, new ResultSetMapping());
        $q->setParameter('unit', 'г');
        $q->setParameter('AOLEVEL4', FiasComponent::AOLEVEL_4_CITY);

        $rows = $q->getResult('ListAssocHydrator');

        $this->getComponent()->getOutput()->writeln('Cities: ' . count($rows) . $this->getComponent()->showMemUsage());

        $cities = [];
        foreach ($rows as $row) {
            $cities[$row['city_id']] = $row['geo_city_id'];
        }

        // Не совсем города
        $sql = "
            SELECT
                gcs.city_id,
                MAX( gc.id ) AS geo_city_id,
                gr.name 
            FROM
                geo_ip gb
                INNER JOIN __geo_cities gcs ON gcs.city_id = gb.city_id
                INNER JOIN geo_city gc ON gcs.city = gc.name
                INNER JOIN geo_region gr ON gr.id = gc.geo_region_id 
            WHERE
                REPLACE (
                REPLACE (
                REPLACE (
                REPLACE (
                REPLACE (
                REPLACE ( REPLACE ( REPLACE ( gcs.region, ' автономная область', '' ), ' область', '' ), 'Республика ', '' ),
                ' автономный округ',
                '' 
                ),
                ' край',
                '' 
                ),
                'Санкт-Петербург и ',
                '' 
                ),
                'Москва и ',
                '' 
                ),
                'Город федерального значения ',
                '' 
                ) = gr.name 
                AND gcs.city_id NOT IN (
                    SELECT DISTINCT
                        gcs.city_id 
                    FROM
                        geo_ip gb
                        INNER JOIN __geo_cities gcs ON gcs.city_id = gb.city_id
                        INNER JOIN geo_city gc ON gcs.city = gc.name
                        INNER JOIN geo_temp gt ON gc.\"AOGUID\" = gt.\"AOGUID\" 
                    WHERE
                        gt.\"AOLEVEL\" = :AOLEVEL4 
                        AND gc.unit = :unit 
                ) 
            GROUP BY
                gb.city_id,
                gcs.city_id,
                gr.name 
            ORDER BY
                gcs.city        
        ";

        $q = $this->getComponent()->getEm()->createNativeQuery($sql, new ResultSetMapping());
        $q->setParameter('unit', 'г');
        $q->setParameter('AOLEVEL4', FiasComponent::AOLEVEL_4_CITY);

        $rows = $q->getResult('ListAssocHydrator');

        $this->getComponent()->getOutput()->writeln('Not cities: ' . count($rows) . $this->getComponent()->showMemUsage());

        foreach ($rows as $row) {
            $cities[$row['city_id']] = $row['geo_city_id'];
        }

        $this->getComponent()->getOutput()->writeln('Total rows: ' . count($cities) . $this->getComponent()->showMemUsage());

        foreach ($cities as $cityId => $geoCityId) {
            $sql = 'UPDATE '.$this->getComponent()->getEm()->getClassMetadata(GeoIp::class)->getTableName().' 
                SET geo_city_id = :geo_city_id  
                WHERE city_id = :city_id';
            $statement = $this->getComponent()->getEm()->getConnection()->prepare($sql);
            $statement->bindValue('geo_city_id', $geoCityId, Type::INTEGER);
            $statement->bindValue('city_id', $cityId, Type::INTEGER);
            $statement->execute();
        }

        $this->getComponent()->getOutput()->writeln('Updated 1...');

        // Не совсем города 2
        $sql = "
            SELECT DISTINCT
                gb.city_id,
                gc.name,
                MAX( gc.id ) AS geo_city_id 
            FROM
                geo_ip gb
                INNER JOIN __geo_cities gcs ON gb.city_id = gcs.city_id
                LEFT JOIN geo_city gc ON gcs.city = gc.name
                LEFT JOIN geo_region gr ON gr.id = gc.geo_region_id
                LEFT JOIN geo_temp gt ON gc.\"AOGUID\" = gt.\"AOGUID\" 
            WHERE
                geo_city_id IS NULL  AND gc.id IS NOT NULL
            GROUP BY
                gb.city_id,
                gc.name
            ORDER BY
                gc.name     
        ";

        $q = $this->getComponent()->getEm()->createNativeQuery($sql, new ResultSetMapping());
        $rows = $q->getResult('ListAssocHydrator');

        $this->getComponent()->getOutput()->writeln('Not cities 2: ' . count($rows) . $this->getComponent()->showMemUsage());

        $cities = [];
        foreach ($rows as $row) {
            $cities[$row['city_id']] = $row['geo_city_id'];
        }

        $this->getComponent()->getOutput()->writeln('Total rows: ' . count($cities) . $this->getComponent()->showMemUsage());

        foreach ($cities as $cityId => $geoCityId) {
            $sql = 'UPDATE '.$this->getComponent()->getEm()->getClassMetadata(GeoIp::class)->getTableName().' 
                SET geo_city_id = :geo_city_id  
                WHERE city_id = :city_id AND geo_city_id IS NULL';
            $statement = $this->getComponent()->getEm()->getConnection()->prepare($sql);
            $statement->bindValue('geo_city_id', $geoCityId, Type::INTEGER);
            $statement->bindValue('city_id', $cityId, Type::INTEGER);
            $statement->execute();
        }

        $sql = 'DELETE FROM '.$this->getComponent()->getEm()->getClassMetadata(GeoIp::class)->getTableName().' WHERE geo_city_id IS NULL';
        $statement = $this->getComponent()->getEm()->getConnection()->prepare($sql);
        $statement->execute();

        $this->getComponent()->getOutput()->writeln('Delete empty geo_city_id...');

        $this->getComponent()->truncateGeoTable(GeoCities::class);
    }
}
