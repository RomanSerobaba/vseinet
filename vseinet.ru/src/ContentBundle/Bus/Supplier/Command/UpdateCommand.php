<?php 

namespace ContentBundle\Bus\Supplier\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class UpdateCommand extends Message
{    
    /**
     * @Assert\NotBlank(message="Value of 'id' should not be blank")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Наименование")
     * @Assert\NotBlank(message="Value of 'name' should not be blank")
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @VIA\Description("Краткое обозначение латиницей")
     * @Assert\NotBlank(message="Value of 'code' should not be blank")
     * @Assert\Type(type="string")
     */
    public $code;

    /**
     * @VIA\Description("Описание/Прмечания")
     * @Assert\Type(type="string")
     */
    public $description;

    /**
     * @VIA\Description("Сайт поставщика")
     * @Assert\Type(type="string")
     */
    public $siteUrl;

    /**
     * @VIA\Description("Адрес авторизации/Личный кабинет")
     * @Assert\Type(type="string")
     */
    public $authUrl;

    /**
     * @VIA\Description("Логин авторизации")
     * @Assert\Type(type="string")
     */
    public $authLogin;

    /**
     * @VIA\Description("Пароль авторизации")
     * @Assert\Type(type="string")
     */
    public $authPassword;

    /**
     * @VIA\Description("Примечания к личному кабинету")
     * @Assert\Type(type="string")
     */
    public $authComment;

    /**
     * @VIA\Description("Идентификатор географического расположения")
     * @Assert\Type(type="integer")
     */
    public $geoPointId;

    /**
     * @VIA\Description("Возможность бесплатной доставки")
     * @Assert\Type(type="boolean")
     * @VIA\DefaultValue(false)
     */
    public $hasFreeDelivery;

    /**
     * @VIA\Description("Идентификатор ответственного менеджера")
     * @Assert\Type(type="integer")
     */
    public $managerId;

    /**
     * @VIA\Description("Признак активности")
     * @Assert\Type(type="boolean")
     * @VIA\DefaultValue(false)
     */
    public $isActive;

}