<?php 

namespace ContentBundle\Bus\BaseProduct\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @internal
 */
class RenameCommand extends Message
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;
    
    /**
     * @Assert\Type(type="integer")
     */
    public $categoryId;

    /**
     * @Assert\Type(type="integer")
     */
    public $brandId;
}