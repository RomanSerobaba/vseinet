<?php 

namespace MatrixBundle\Bus\Representative\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class SetMatrixLimitCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Значение id не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\NotBlank(message="Значение value не должно быть пустым") 
     * @Assert\Type(type="integer")
     * @Assert\Range(
     *      min = 0,
     *      max = 2147483647,
     *      minMessage = "Лимит стоимости товара на точке не может быть меньше {{ limit }}",
     *      maxMessage = "Лимит стоимости товара на точке не может быть больше {{ limit }}"
     * )
     */
    public $value;
}