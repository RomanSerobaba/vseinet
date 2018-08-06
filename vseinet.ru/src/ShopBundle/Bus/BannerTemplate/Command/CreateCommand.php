<?php 

namespace ShopBundle\Bus\BannerTemplate\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class CreateCommand extends Message
{
    /**
     * @VIA\Description("Шаблон название")
     * @Assert\NotBlank(message="Название шаблона не указано")
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="string")
     */
    public $imgBackgroundPc;

    /**
     * @Assert\Type(type="string")
     */
    public $imgBackgroundTablet;

    /**
     * @Assert\Type(type="string")
     */
    public $imgBackgroundPhone;

    /**
     * @Assert\Type(type="integer")
     */
    public $posBackgroundPcX;

    /**
     * @Assert\Type(type="integer")
     */
    public $posBackgroundPcY;

    /**
     * @Assert\Type(type="integer")
     */
    public $posBackgroundTabletX;

    /**
     * @Assert\Type(type="integer")
     */
    public $posBackgroundTabletY;

    /**
     * @Assert\Type(type="integer")
     */
    public $posBackgroundPhoneX;

    /**
     * @Assert\Type(type="integer")
     */
    public $posBackgroundPhoneY;

    /**
     * @Assert\Uuid
     */
    public $uuid;
}