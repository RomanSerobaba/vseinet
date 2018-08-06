<?php

namespace DocumentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Документ - Претензия - Шапка
 *
 * @ORM\Table(name="any_doc_comment")
 * @ORM\Entity()
 */

class Comment
{
    
    // <editor-fold defaultstate="collapsed" desc="Поля">    
    
    ///////////////////////////
    //
    //  Поля
    //
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="any_doc_did", type="integer")
     */
    private $documentId;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="created_by", type="integer", nullable=true)
     */
    private $createdBy;
    
    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string")
     */
    private $comment;

    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Методы">    
    
    ///////////////////////////
    //
    //  Методы
    //
    
    // field id 

    /**
     * Получить идентификатор
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    // field documentId

    /**
     * Получить идентификатор документа
     *
     * @return int
     */
    public function getDocumentId()
    {
        return $this->documentId;
    }

    /**
     * Установить идентификатор претензии
     *
     * @param int $documentId
     *
     * @return GoodsIssueDocComment
     */
    public function setDocumentId($documentId)
    {
        $this->documentId = $documentId;

        return $this;
    }

    // field createdAt
    
    /**
     * Получить дату создания докуменнта
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Установить дату создания документа
     *
     * @param \DateTime $createdAt
     *
     * @return GoodsIssueDocComment
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }
    
    // field createdBy
    
    /**
     * Получить идентификатор автора документа
     *
     * @return int|null
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Установить идентификатор автора докмуента
     *
     * @param int|null $createdBy
     *
     * @return GoodsIssueDocComment
     */
    public function setCreatedBy($createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    // field title
    
    /**
     * Получить текст комментария
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Установить текст комментария
     *
     * @param string $comment
     *
     * @return GoodsIssueDocComment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }
    
    ///////////////////////////////////////////////

    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Переопределение магических методов">
    
    ///////////////////////////
    //
    //  Переопределение магических методов
    //
    
    function __construct()
    {
        $this->createdAt = new \DateTime;
    }
    
    function __clone()
    {
        $this->id = null;
    }
    
    // </editor-fold>
    
}
