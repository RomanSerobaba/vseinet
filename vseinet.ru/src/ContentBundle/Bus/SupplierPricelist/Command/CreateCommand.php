<?php 

namespace ContentBundle\Bus\SupplierPricelist\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class CreateCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Значение supplierId не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $supplierId;

    /**
     * @Assert\NotBlank(message="Значение name не должно быть пустым")
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="boolean")
     * @VIA\DefaultValue(false)
     */
    public $isMulti;

    /**
     * @Assert\Uuid
     */
    public $uuid;
}