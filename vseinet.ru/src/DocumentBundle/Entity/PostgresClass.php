<?php

namespace DocumentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * DocumentStatus
 * @ORM\Table(name="pg_class")
 * @ORM\Entity()
 */
class PostgresClass
{
    
    // <editor-fold defaultstate="collapsed" desc="Поля">    
    
    /**
     * @var integer
     * @ORM\id
     * @ORM\Column(name="oid", type="integer")
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $OID;

    /**
     * @var string
     * @ORM\Column(name="relname", type="string")
     */
    private $relName;
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Методы">    
    
    /**
     * Получить системный идентификатор объекта
     * @return integer
     */
    public function getOID()
    {
        return $this->OID;
    }

    /**
     * Получить наименование объекта в БД
     * @return string
     */
    public function getRelName()
    {
        return $this->relName;
    }
    
    // </editor-fold>
}
