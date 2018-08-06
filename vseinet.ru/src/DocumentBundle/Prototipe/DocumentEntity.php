<?php
/**
 * @author Denis O. Konashonok
 */
namespace DocumentBundle\Prototipe;

use Doctrine\ORM\Mapping as ORM;

trait DocumentEntity
{

    // <editor-fold defaultstate="collapsed" desc="Поля">    
    
    ///////////////////////////////////////////////////////////
    //
    //  Поля
    //
    
    /**
     * @var int
     * @ORM\Column(name="did", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\SequenceGenerator(sequenceName="any_doc_did_seq")
     */
    private $dId;
    
    /**
     * @var string
     * @ORM\Column(name="status_code", type="string")
     */
    private $statusCode;
    
    /**
     * @var int
     * @ORM\Column(name="parent_doc_did", type="integer")
     */
    private $parentDocumentId;
    
    /**
     * @var integer
     * @ORM\Column(name="number", type="integer")
     */
    private $number;

    /**
     * @var string
     * @ORM\Column(name="title", type="string")
     */
    private $title;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;
    
    /**
     * @var integer
     * @ORM\Column(name="created_by", type="integer", nullable=true)
     */

    private $createdBy;

    /**
     * @var \DateTime
     * @ORM\Column(name="completed_at", type="datetime", nullable=true)
     */
    private $completedAt;
    
    /**
     * @var integer
     * @ORM\Column(name="completed_by", type="integer", nullable=true)
     */
    private $completedBy;

    /**
     * @var \DateTime
     * @ORM\Column(name="registered_at", type="datetime", nullable=true)
     */
    private $registeredAt;
    
    /**
     * @var integer
     * @ORM\Column(name="registered_by", type="integer", nullable=true)
     */
    private $registeredBy;

    /////////////////////////////////////    

    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Методы">    
    
    ///////////////////////////////////////////////////////////
    //
    //  Методы
    //

    // field did 

    /**
     * Получить идентификатор
     * @return int
     */
    public function getDId()
    {
        return $this->dId;
    }

    // field statusCode

    /**
     * Получить статус документа
     * @return string
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Установить статус документа
     * @param string $statusCode
     * @return object
     */
    public function setStatusCode(string $statusCode = 'new')
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    // field parentDocumentId

    /**
     * Получить универсальный идентификатор документа-основания (родителя)
     * @return int|null
     */
    public function getParentDocumentId()
    {
        return $this->parentDocumentId;
    }

    /**
     * Установить идентификатор документа-основания (родителя)
     * @param int|null $parentDocumentId
     * @return object
     */
    public function setParentDocumentId($parentDocumentId = null)
    {
        $this->parentDocumentId = $parentDocumentId;
        return $this;
    }

    // field number

    /**
     * Получить номер документа
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Установить номер документа
     * @param int $number
     * @return object
     */
    public function setNumber(int $number)
    {
        $this->number = $number;
        return $this;
    }

    // field title
    
    /**
     * Получить заголовок (краткое описание)
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Установить заголовок (краткое описание)
     * @param string $title
     * @return object
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }
    // field createdAt
    
    /**
     * Получить дату создания докуменнта
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Установить дату создания документа
     * @param \DateTime $createdAt
     * @return object
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }
    
    // field createdBy
    
    /**
     * Получить идентификатор автора документа
     * @return int|null
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Установить идентификатор автора докмуента
     * @param int|null $createdBy
     * @return object
     */
    public function setCreatedBy($createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    // field completedAt
    
    /**
     * Получение даты завершения (закрытия) документа
     * @return \DateTime|null
     */
    public function getCompletedAt()
    {
        return $this->completedAt;
    }

    /**
     * Установка даты завершения (закрытия) документа
     * @param \DateTime|null $completedAt
     * @return object
     */
    public function setCompletedAt($completedAt = null)
    {
        $this->completedAt = $completedAt;
        
        return $this;
    }

    // field completedBy
    
    /**
     * Получить идентификатор пользователя закрывшего документ
     * @return int|null
     */
    public function getCompletedBy()
    {
        return $this->completedBy;
    }

    /**
     * Установить идентификатор пользователя закрывшего докмуент
     * @param int|null $completedBy
     * @return object
     */
    public function setCompletedBy($completedBy = null)
    {
        $this->completedBy = $completedBy;

        return $this;
    }

    // field registeredAt
    
    /**
     * Получение даты проведения документа
     * @return \DateTime|null
     */
    public function getRegisteredAt()
    {
        return $this->registeredAt;
    }

    /**
     * Установка даты проведения документа
     * @param \DateTime|null $registeredAt
     * @return object
     */
    public function setRegisteredAt($registeredAt = null)
    {
        $this->registeredAt = $registeredAt;
        
        return $this;
    }

    // field registeredBy
    
    /**
     * Получить идентификатор пользователя проводившего документ
     * @return int|null
     */
    public function getRegisteredBy()
    {
        return $this->registeredBy;
    }

    /**
     * Установить идентификатор пользователя проводившего докмуент
     * @param int|null $registeredBy
     * @return object
     */
    public function setRegisteredBy($registeredBy = null)
    {
        $this->registeredBy = $registeredBy;

        return $this;
    }

    // </editor-fold>

    function __construct()
    {

        $this->id = null;
        $this->createdAt = new \DateTime;

    }

    function __clone()
    {

        $this->id = null;
        $this->createdAt = new \DateTime;
        $this->createdBy = NULL;
        
    }

}

