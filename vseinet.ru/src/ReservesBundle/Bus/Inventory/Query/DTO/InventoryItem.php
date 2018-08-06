<?php 

namespace ReservesBundle\Bus\Inventory\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class InventoryItem
{
    /**
     * @VIA\Description("Идентификатор документа")
     * @Assert\Type(type="integer")
     */
    private $id;

    /**
     * @VIA\Description("Номер документа")
     * @Assert\Type(type="integer")
     */
    private $number;

    /**
     * @VIA\Description("Дата создания документа")
     * @Assert\DateTime
     */
    private $createdAt;
    
    /**
     * @VIA\Description("Идентификатор автора")
     * @Assert\Type(type="integer")
     */
    private $createdBy;
    
    /**
     * @VIA\Description("Наименование автора")
     * @Assert\Type(type="string")
     */
    private $createdName;
    
    /**
     * @VIA\Description("Наименование документа")
     * @Assert\Type(type="string")
     */
    private $title;
    
    /**
     * @VIA\Description("Идентификатор геоточки")
     * @Assert\Type(type="string")
     */
    private $geoRoomId;
    
    /**
     * @VIA\Description("Наименование геоточки")
     * @Assert\Type(type="string")
     */
    private $geoRoomName;
    
    /**
     * @VIA\Description("Идентификатор ответственного")
     * @Assert\Type(type="integer")
     */
    private $responsibleId;
    
    /**
     * @VIA\Description("Наименование ответственного")
     * @Assert\Type(type="string")
     */
    private $responsibleName;
    
    /**
     * @VIA\Description("Дата закрытия документа")
     * @Assert\DateTime
     */
    private $completedAt;
    
    /**
     * @VIA\Description("Статус документа")
     * @Assert\Type(type="string")
     */
    private $status;
    
    /**
     * @VIA\Description("Категории документа")
     * @Assert\Type(type="array<integer>")
     */
    private $categories;

    /**
     * @VIA\Description("Участники инвентаризации")
     * @Assert\Type(type="array<integer>")
     */
    public $participants;

    public function __construct($id, $number, $createdAt, $createdBy, $createdName, $title, $geoRoomId, $geoRoomName, $responsibleId, $responsibleName, $completedAt, $status, $categories = [], $participants = [])
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
        $this->categories = empty($categories) ? [] : $categories;
        $this->participants = empty($participants) ? [] : $participants;
    }
}