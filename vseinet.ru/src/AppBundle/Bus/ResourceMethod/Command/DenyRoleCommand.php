<?php 

namespace AppBundle\Bus\ResourceMethod\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class DenyRoleCommand extends Message 
{
    /**
     * @Assert\NotBlank(message="Значение subroleId не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $subroleId;

    /**
     * @Assert\NotBlank(message="Значение methodId не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $methodId;
}