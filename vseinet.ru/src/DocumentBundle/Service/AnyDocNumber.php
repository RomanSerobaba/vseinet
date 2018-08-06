<?php

namespace DocumentBundle\Service;

use AppBundle\Container\ContainerAware;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\EntityManager;

class AnyDocNumber
{

    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Получить следующий номер документа
     *
     * @param string $entityClassName
     * @return type
     */
    public function nextValue(string $entityClassName)
    {

        $documentTableName = $this->em
                        ->getClassMetadata($entityClassName)->getTableName();

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id', 'integer');
        $documentNumber = $this->em->createNativeQuery(
                        "select nextval('{$documentTableName}_number_seq'::regclass) as id;", $rsm)->getSingleScalarResult();

        return $documentNumber;
    }

}
