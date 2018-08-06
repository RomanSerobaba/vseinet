<?php 

namespace SiteBundle\Bus\Catalog\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetProductsQuery extends Message
{
    /**
     * @Assert\NotBlank
     * @Assert\All({
     *     @Assert\Type(type="integer")
     * })
     */
    public $ids;

    /**
     * @Assert\Choice({"xs", "sm", "md", "lg", "xl"}, strict=true)
     */
    public $previewSize = 'md';
}
