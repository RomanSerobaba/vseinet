<?php 

namespace ContentBundle\Bus\Detail\Converter;

use Doctrine\ORD\EntityManagerInterface;

class Resolver
{
    const RE_BOOLEAN = '/(да|есть|нет|\+)(.*)/iu';

    const DIMENSIONS = '/^(\d*[\.|,]?\d+)[^\d,\.]+(\d*[\.|,]?\d+)[^\d,\.]+(\d*[\.|,]?\d+)\s?(.*)/u';

    const RE_SIZE = '/^(\d*[\.|,]?\d+)[^\d,\.]+(\d*[\.|,]?\d+)\s?(.*)/u';

    const RE_NUMBER = '/^(-?\d*[\.|,]?\d+)\s?(.*)/u';


    /**
     * @var EntityManagerInterface
     */
    protected $em;


    /**
     * @param EntityManagerInterface $em 
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Analyze type str
     * 
     * @param string $str
     * 
     * @return <string,integer|null>
     */
    public function analyze($str)
    {
        if (100 < mb_strlen($str, 'UTF-8')) {
            return [
                DetailType::CODE_MEMO,
                null,
            ];
        }

        if (preg_match(self::RE_BOOLEAN, $str)) {
            return [
                DetailType::CODE_BOOLEAN,
                null,
            ];
        }

        if (preg_match(self::DIMENSIONS, $str, $matches)) {
            return [
                DetailType::CODE_DIMENSIONS,
                $this->getUnitId($matches[4]),
            ];
        }

        if (preg_match(self::SIZE, $str, $matches)) {
            return [
                DetailType::CODE_SIZE,
                $this->getUnitId($matches[3]),
            ];
        }

        if (preg_match(self::NUMBER, $str, $matches)) {
            return [
                DetailType::CODE_NUMBER,
                $this->getUnitId($matches[2]),
            ];
        }

        return [
            DetailType::CODE_STRING,
            null,
        ]
    }

    public function getUnitId($str)
    {
        $connection = $this->em->getConnection();

        $set = array_map(function($item) use ($connection) {
            return $connection->quote($item);
        }, array_filter(array_map('trim', preg_split('/[\s,\.]+/', $str))));

        $q = $connection->prepare("
            WITH 
                data (str, set) AS (
                    VALUES (:str, '".implode("','", $set)."')
                )
            SELECT mu.id, CASE WHEN mu.name = data.str THEN 1 WHEN mua.name = data.str THEN 2 ELSE 3 END ORD  
            FROM content_measure_unit mu 
            INNER JOIN data
            LEFT OUTER JOIN content_measure_unit_alias mua ON mua.content_measure_unit_id = mu.id 
            WHERE mu.name IN (data.set) OR mua.name IN (data.set)
            LIMIT 1
        ");
        $q->execute(['str' => trim($str)]);

        return $q->fetchColumn();
    }
}