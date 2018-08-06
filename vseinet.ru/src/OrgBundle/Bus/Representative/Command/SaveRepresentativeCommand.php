<?php 

namespace OrgBundle\Bus\Representative\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class SaveRepresentativeCommand extends Message
{
    /**
     * @VIA\Description("Representative id OR null if new")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Geo point id if new")
     * @Assert\Type(type="integer")
     */
    public $geoPointId;

    /**
     * @VIA\Description("Есть склад")
     * @Assert\Type(type="boolean")
     */
    public $hasWarehouse;

    /**
     * @VIA\Description("Точка самовывоза")
     * @Assert\Type(type="boolean")
     */
    public $hasRetail;

    /**
     * @VIA\Description("Есть сервисный отдел")
     * @Assert\Type(type="boolean")
     */
    public $hasOrderIssueing;

    /**
     * @VIA\Description("Есть доставка")
     * @Assert\Type(type="boolean")
     */
    public $hasDelivery;

    /**
     * @VIA\Description("Возможен занос/подъем на этаж")
     * @Assert\Type(type="boolean")
     */
    public $hasRising;

    /**
     * @VIA\Description("Активная")
     * @Assert\Type(type="boolean")
     */
    public $isActive;

    /**
     * @VIA\Description("Центральная, Главная точка")
     * @Assert\Type(type="boolean")
     */
    public $isCentral;

    /**
     * @VIA\Description("Транзит на другие точки")
     * @Assert\Type(type="boolean")
     */
    public $hasTransit;

    /**
     * @VIA\Description("Тип")
     * @Assert\NotBlank
     * @Assert\Choice({"our", "franchiser", "partner", "torg"}, strict=true)
     */
    public $type;

    /**
     * @VIA\Description("IP")
     * @Assert\Type(type="string")
     */
    public $ip;

    /**
     * @VIA\Description("Тариф доставки")
     * @Assert\Type(type="integer")
     */
    public $deliveryTax;

    /**
     * @VIA\Description("Название")
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    public $geoPointName;

    /**
     * @VIA\Description("Код")
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    public $geoPointCode;

    /**
     * @VIA\Description("Адрес")
     * @Assert\Type(type="string")
     */
    public $address;

    /**
     * @VIA\Description("Координаты")
     * @Assert\Type(type="string")
     */
    public $coordinates;

    /**
     * @VIA\Description("Изображение представительства")
     * @Assert\File(
     *     mimeTypes={
     *         "image/jpeg",
     *         "image/png"
     *     }
     * )
     */
    public $photo;

    /**
     * @VIA\Description("Дополнительное изображение представительства")
     * @Assert\File(
     *     mimeTypes={
     *         "image/jpeg",
     *         "image/png"
     *     }
     * )
     */
    public $photo1;

    /**
     * @VIA\Description("Дополнительное изображение представительства")
     * @Assert\File(
     *     mimeTypes={
     *         "image/jpeg",
     *         "image/png"
     *     }
     * )
     */
    public $photo2;

    /**
     * @VIA\Description("Дополнительное изображение представительства")
     * @Assert\File(
     *     mimeTypes={
     *         "image/jpeg",
     *         "image/png"
     *     }
     * )
     */
    public $photo3;

    /**
     * @VIA\Description("Дополнительное изображение представительства")
     * @Assert\File(
     *     mimeTypes={
     *         "image/jpeg",
     *         "image/png"
     *     }
     * )
     */
    public $photo4;


    /**
     * @VIA\Description("Расписание ['s1' => '9:00', 't1'=> '18:00', ...]")
     * @Assert\Type(type="array")
     */
    public $schedule;
}