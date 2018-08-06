<?php 

namespace AccountingBundle\Bus\Clients\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use AppBundle\Validator\Constraints\MobilePhone;
use Symfony\Component\Validator\Constraints as Assert;

class SaveClientCommand extends Message
{
    /**
     * @VIA\Description("User id")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Фамилия")
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    public $lastname;

    /**
     * @VIA\Description("Имя")
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    public $firstname;

    /**
     * @VIA\Description("Отчество")
     * @Assert\Type(type="string")
     */
    public $secondname;

    /**
     * @VIA\Description("Город")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $cityId;

    /**
     * @VIA\Description("Пол")
     * @Assert\Choice({"male", "female"}, strict=true)
     */
    public $gender;

    /**
     * @VIA\Description("Телефон")
     * @Assert\Type(type="array")
     */
    public $phones;

    /**
     * @VIA\Description("Мобильный")
     * @Assert\Type(type="string")
     * @MobilePhone
     */
    public $mobile;

    /**
     * @Assert\NotBlank(message="Значение contact mobile id не должно быть пустым")
     * @Assert\Type(type="integer")
     */

    public $contactMobileId;

    /**
     * @Assert\NotBlank(message="Значение email не должно быть пустым")
     * @Assert\Type(type="string")
     * @Assert\Email
     */
    public $email;

    /**
     * @Assert\NotBlank(message="Значение contact email id не должно быть пустым")
     * @Assert\Type(type="integer")
     */

    public $contactEmailId;

    /**
     * @VIA\Description("Skype")
     * @Assert\Type(type="string")
     */
    public $skype;

    /**
     * @VIA\Description("Icq")
     * @Assert\Type(type="string")
     */
    public $icq;

    /**
     * @VIA\Description("Is marketing subscribed")
     * @Assert\Type(type="boolean")
     */
    public $isMarketingSubscribed;

    /**
     * @VIA\Description("Is transactional subscribed")
     * @Assert\Type(type="boolean")
     */
    public $isTransactionalSubscribed;

    /**
     * @Assert\Type(type="string")
     */
    public $password;
}