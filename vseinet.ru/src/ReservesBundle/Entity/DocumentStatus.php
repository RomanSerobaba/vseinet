<?php

namespace ReservesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * DocumentStatus
 * @ORM\Table(name="any_doc_status")
 */
class DocumentStatus
{
    
    // <editor-fold defaultstate="collapsed" desc="Поля">    
    
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="doc_table_name", type="string")
     */
    private $documentTableName;
    
    /**
     * @var string
     * @ORM\Column(name="name", type="string")
     */
    private $name;
    
    /**
     * @var boolean
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Методы">    
    
    /**
     * Получить уникальный идентификатор
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Установить наименование таблицы документов
     * @param string $documentTableName
     * @return DocumentStatus
     */
    public function setDocumentTableName($documentTableName)
    {
        $this->documentTableName = $documentTableName;

        return $this;
    }

    /**
     * Получить наименование таблицы документов
     * @return string
     */
    public function getDocumentTableName()
    {
        return $this->documentTableName;
    }

    /**
     * Установить наименование статуса
     * @param string $name
     * @return DocumentStatus
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Получить наименование статуса
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Установить активность статуса
     * @param boolean $active
     * @return DocumentStatus
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Получить активность статуса
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    // </editor-fold>

    
}
