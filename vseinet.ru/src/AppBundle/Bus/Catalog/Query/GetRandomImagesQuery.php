<?php 

namespace AppBundle\Bus\Catalog\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetRandomImagesQuery extends Message 
{
    /**
     * @Assert\All({
     *     @Assert\Type(type="integer")
     * })
     */
    public $ids;
}