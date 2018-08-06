<?php 

namespace ContentBundle\Bus\BaseProduct\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class UpdateCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Значение id не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string") 
     */
    public $model;

    /**
     * @VIA\Description("Ссылка на сайт производителя")
     * @Assert\Type(type="string")
     * @Assert\Url(checkDNS=true, message="Невалиндная ссылка на сайт производителя")
     */
    public $manufacturerLink;

    /**
     * @VIA\Description("Ссылка на инструкцию")
     * @Assert\Type(type="string")
     * @Assert\Url(checkDNS=true, message="Невалидная ссылка на инструкцию")
     */
    public $manualLink;

    /**
     * @Assert\Type(type="string")
     */
    public $description; 
}