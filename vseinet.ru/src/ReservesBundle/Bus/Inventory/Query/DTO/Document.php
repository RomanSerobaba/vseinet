<?php 

namespace ReservesBundle\Bus\Inventory\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class Document
{
    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Универсальный идентификатор документа")
     */
    private $id;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Номер документа")
     */
    private $number;

    /**
     * @Assert\DateTime
     * @VIA\Description("Дата создания документа")
     */
    private $createdAt;
    
    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Идентификатор автора")
     */
    private $createdBy;
    
    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Наименование автора")
     */
    private $createdName;
    
    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Наименование документа")
     */
    private $title;
    
    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Идентификатор геоточки")
     */
    private $geoRoomId;
    
    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Наименование геоточки")
     */
    private $geoRoomName;
    
    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Идентификатор ответственного")
     */
    private $responsibleId;
    
    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Наименование ответственного")
     */
    private $responsibleName;
    
    /**
     * @Assert\DateTime
     * @VIA\Description("Дата закрытия документа")
     */
    private $completedAt;
    
    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Статус документа")
     */
    private $status;
    
    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Уровень пользователя - owner/автор, responsible/ответственный, participant/участник")
     */
    private $userLevel;
    
    public function __construct($id, $number, $createdAt, $createdBy, $createdName, $title, $geoRoomId, $geoRoomName, $responsibleId, $responsibleName, $completedAt, $status, $userLevel)
    {
        $this->id = $id;
        $this->number = $number;
        $this->createdAt = $createdAt;
        $this->createdBy = $createdBy;
        $this->createdName = $createdName;
        $this->title = $title;
        $this->geoRoomId = $geoRoomId;
        $this->geoRoomName = $geoRoomName;
        $this->responsibleId = $responsibleId;
        $this->responsibleName = $responsibleName;
        $this->completedAt = $completedAt;
        $this->status = $status;
        $this->userLevel = $userLevel;
    }
}