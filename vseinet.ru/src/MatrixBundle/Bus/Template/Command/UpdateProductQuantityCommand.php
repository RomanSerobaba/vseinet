<?php 

namespace MatrixBundle\Bus\Template\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class UpdateProductQuantityCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Значение id не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\NotBlank(message="Значение baseProductId не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $baseProductId;

    /**
     * @Assert\Type(type="integer")
     * @VIA\DefaultValue(0)
     */
    public $quantity;
}