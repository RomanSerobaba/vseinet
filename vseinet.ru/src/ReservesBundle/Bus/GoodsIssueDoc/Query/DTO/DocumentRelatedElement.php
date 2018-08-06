<?php 

namespace ReservesBundle\Bus\GoodsIssueDoc\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class DocumentRelatedElement
{
    /**
     * @VIA\Description("Идентификатор элемнта/Универсальный идентификатор документа")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Тип элемента. Можен принимать значения типов документов или строка 'comment'.")
     * @Assert\Type(type="string")
     */
    public $type;
    
    /**
     * @VIA\Description("Дата создания")
     * @Assert\DateTime
     * @Assert\Type(type="datetime")
     */
    public $createdAt;

    /**
     * @VIA\Description("Идентификатор автора")
     * @Assert\Type(type="integer")
     */
    public $createdBy;

    /**
     * @VIA\Description("Наименование автора")
     * @Assert\Type(type="string")
     */
    public $createdName;

    /**
     * @VIA\Description("Дата закрытия документа")
     * @Assert\DateTime
     * @Assert\Type(type="datetime")
     */
    public $completedAt;
    
    /**
     * @VIA\Description("Идентификатор пользователя закрывшего документ")
     * @Assert\Type(type="integer")
     */
    public $completedBy;
    
    /**
     * @VIA\Description("Наименование пользователя закрывшего документ")
     * @Assert\Type(type="string")
     */
    public $completedName;
    
    /**
     * @VIA\Description("Дата проведения документа")
     * @Assert\DateTime
     * @Assert\Type(type="datetime")
     */
    public $registeredAt;
    
    /**
     * @VIA\Description("Идентификатор пользователя проведшего документ")
     * @Assert\Type(type="integer")
     */
    public $registeredBy;
    
    /**
     * @VIA\Description("Наименование пользователя проведшего документ")
     * @Assert\Type(type="string")
     */
    public $registeredName;
    
    /**
     * @VIA\Description("Человекочитаемый заголовок")
     * @Assert\Type(type="string")
     */
    public $title;

    /**
     * @VIA\Description("Количество решённого по товару")
     * @Assert\Type(type="integer")
     */
    public $sumGoods;

    /**
     * @VIA\Description("Количество решённого по клиенту")
     * @Assert\Type(type="integer")
     */
    public $sumClient;

    /**
     * @VIA\Description("Количество решённого по поставщику")
     * @Assert\Type(type="integer")
     */
    public $sumSupplier;
    
}