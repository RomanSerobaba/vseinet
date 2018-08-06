<?php 

namespace MatrixBundle\Bus\Representative\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class CopyMatrixCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Значение id не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\NotBlank(message="Значение sourceRepresentativeId не должно быть пустым") 
     * @Assert\Type(type="integer")
     */
    public $sourceRepresentativeId;
}