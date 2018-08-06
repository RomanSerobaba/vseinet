<?php

namespace ReservesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * DocumentStatus
 * @ORM\Table(name="any_doc_status")
 */
class DocumentStatusHistory
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
     * @var integer
     * @ORM\Column(name="any_doc_status_id", type="integer")
     */
    private $statusId;
    
    /**
     * @var integer
     * @ORM\Column(name="document_id", type="integer")
     */
    private $documentId;
    
    /**
     * @var \DateTime
     * @ORM\Column(name="selected_at", type="timestamp")
     */
    private $selectedAt;
    
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
     * Установить идентификатор статуса
     * @param integer $statusId
     * @return DocumentStatusHistory
     */
    public function setStatusId($statusId)
    {
        $this->statusId = $statusId;

        return $this;
    }

    /**
     * Получить идентификатор статуса
     * @return integer
     */
    public function getStatusId()
    {
        return $this->statusId;
    }

    /**
     * Установить идентификатор документа
     * @param integer $documentId
     * @return DocumentStatusHistory
     */
    public function setDocumentId($documentId)
    {
        $this->documentId = $documentId;

        return $this;
    }

    /**
     * Получить идентификатор документа
     * @return integer
     */
    public function getDocumentId()
    {
        return $this->documentId;
    }

    /**
     * Установить дату выбора статуса
     * @param \DateTime $selectedAt
     * @return DocumentStatusHistory
     */
    public function setSelectedAt($selectedAt)
    {
        $this->selectedAt = $selectedAt;

        return $this;
    }

    /**
     * Получить активность статуса
     * @return \DateTime
     */
    public function getSelectedAt()
    {
        return $this->selectedAt;
    }

    // </editor-fold>
}
