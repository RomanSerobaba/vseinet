<?php 

namespace ReservesBundle\Bus\GoodsPallet\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GoodsPallet
{
    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Идентификатор паллеты")
     */
    private $id;

    /**
     * @Assert\DateTime
     * @VIA\Description("Дата создания")
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
     * @VIA\Description("Обозначение паллеты")
     */
    private $title;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Идентификатор получателя")
     */
    private $geoPointId;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Наименование получателя")
     */
    private $geoPointName;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Статус паллеты")
     */
    private $status;

    public function __construct($id, $createdAt, $createdBy, $createdName, $title, $geoPointId, $geoPointName, $status)
    {
        $this->id = $id;
        $this->createdAt = $createdAt;
        $this->createdBy = $createdBy;
        $this->createdName = $createdName;
        $this->title = $title;
        $this->geoPointId = $geoPointId;
        $this->geoPointName = $geoPointName;
        $this->status = $status;
    }
}