<?php 

namespace SiteBundle\Bus\Catalog\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetSubcategoriesQuery extends Message
{
    /**
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $pid;

    /**
     * @Assert\Choice({"xs", "sm", "md", "lg", "xl"}, strict=true)
     */
    public $previewSize = 'md';
}
