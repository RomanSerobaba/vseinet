<?php 

namespace SiteBundle\Bus\Catalog\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetCategoryQuery extends Message
{
    /**
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="SiteBundle\Bus\Brand\Query\DTO\Brand")
     */
    public $brand;

}
