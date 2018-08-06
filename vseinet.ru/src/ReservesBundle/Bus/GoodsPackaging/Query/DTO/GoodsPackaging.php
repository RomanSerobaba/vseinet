<?php 

namespace ReservesBundle\Bus\GoodsPackaging\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GoodsPackaging
{
    /**
     * @VIA\Description("Уникальный идентификатор документа")
     * @Assert\Type(type="integer")
     */
    private $id;

    /**
     * @VIA\Description("Номер документа")
     * @Assert\Type(type="integer")
     */
    private $number;

    /**
     * @VIA\Description("Заголовок документа")
     * @Assert\Type(type="string")
     */
    private $title;

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
     * @VIA\Description("Дата утверждения документа")
     * @Assert\DateTime
     */
    private $completedAt;
    
    /**
     * @VIA\Description("Идентификатор утвержденца")
     * @Assert\Type(type="integer")
     */
    private $completedBy;
    
    /**
     * @VIA\Description("Наименование утвержденца")
     * @Assert\Type(type="string")
     */
    private $completedName;
    
    
    /**
     * @VIA\Description("Идентификатор геоточки")
     * @Assert\Type(type="integer")
     */
    private $geoRoomId;
    
    /**
     * @VIA\Description("Наименование геоточки")
     * @Assert\Type(type="string")
     */
    private $geoRoomName;
    
    /**
     * @VIA\Description("Идентификатор товара")
     * @Assert\Type(type="integer")
     */
    private $baseProductId;
    
    /**
     * @VIA\Description("Наименование товара")
     * @Assert\Type(type="string")
     */
    private $baseProductName;
    
    /**
     * @VIA\Description("Количество")
     * @Assert\Type(type="integer")
     */
    private $quantity;
    
    /**
     * @VIA\Description("Тип документа")
     * @Assert\Type(type="string")
     */
    private $type;
    
    public function __construct($id, $number, $title, $createdAt, $createdBy, $createdName, $completedAt, $completedBy, $completedName, $geoRoomId, $geoRoomName, $baseProductId, $baseProductName, $quantity, $type)
    {
        $this->id = $id;
        $this->number = $number;
        $this->title = $title;
        $this->createdAt = $createdAt;
        $this->createdBy = $createdBy;
        $this->createdName = $createdName;
        $this->completedAt = $completedAt;
        $this->completedBy = $completedBy;
        $this->completedName = $completedName;
        $this->geoRoomId = $geoRoomId;
        $this->geoRoomName = $geoRoomName;
        $this->baseProductId = $baseProductId;
        $this->baseProductName = $baseProductName;
        $this->quantity = $quantity;
        $this->type = $type;
    }
}