<?php 

namespace MatrixBundle\Bus\Representative\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class UpdateCategoryQuantityCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Значение id не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\NotBlank(message="Значение categoryId не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $categoryId;

    /**
     * @Assert\NotBlank(message="Значение quantity не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $quantity;
}