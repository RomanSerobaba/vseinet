<?php 

namespace SupplyBundle\Bus\Suppliers\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class SaveSupplierCommand extends Message
{
    /**
     * @VIA\Description("Supplier id, 0 if new")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Название")
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @VIA\Description("Код")
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    public $code;

    /**
     * @VIA\Description("Ответственный")
     * @Assert\Type(type="integer")
     */
    public $managerId;

    /**
     * @VIA\Description("Бесплатная доставка")
     * @Assert\Type(type="boolean")
     */
    public $hasFreeDelivery;

    /**
     * @VIA\Description("Активен")
     * @Assert\Type(type="boolean")
     */
    public $isActive;

    /**
     * @VIA\Description("Есть договор (дата окончания договора)")
     * @Assert\Type(type="datetime")
     */
    public $contractTill;

    /**
     * @VIA\Description("Ссылка на сайт поставщика")
     * @Assert\Type(type="string")
     */
    public $siteUrl;

    /**
     * @VIA\Description("Краткое описание")
     * @Assert\Type(type="string")
     */
    public $description;

    /**
     * @VIA\Description("Ссылка на личный кабинет")
     * @Assert\Type(type="string")
     */
    public $authUrl;

    /**
     * @VIA\Description("Логин")
     * @Assert\Type(type="string")
     */
    public $authLogin;

    /**
     * @VIA\Description("Пароль")
     * @Assert\Type(type="string")
     */
    public $authPassword;

    /**
     * @VIA\Description("Комментарий к личному кабинету")
     * @Assert\Type(type="string")
     */
    public $authComment;


    /* Юр. лицо */

    /**
     * @VIA\Description("Наименование")
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    public $counteragentName;

    /**
     * @VIA\Description("ИНН")
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    public $tin;

    /**
     * @VIA\Description("КПП")
     * @Assert\Type(type="string")
     */
    public $kpp;

    /**
     * @VIA\Description("ОГРН")
     * @Assert\Type(type="string")
     */
    public $ogrn;

    /**
     * @VIA\Description("ОКПО")
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    public $okpo;

    /**
     * @VIA\Description("НДС")
     * @Assert\Type(type="integer")
     */
    public $vatRate;


    /* Контакты */

    /**
     * @VIA\Description("Контакты")
     * @Assert\Type(type="array")
     *
     * поля:
     *      person_id
     *      firstname
     *      secondname
     *      lastname
     *      position
     *      phone
     *      email
     *      skype
     *      comment
     */
    public $contacts;
}