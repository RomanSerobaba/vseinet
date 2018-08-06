<?php 

namespace SupplyBundle\Bus\Suppliers\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class EditCounteragentCommand extends Message
{
    /**
     * @VIA\Description("Supplier id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Сounteragent id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $cid;

    /**
     * @VIA\Description("Наименование")
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    public $name;

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
}