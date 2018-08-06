<?php 

namespace SiteBundle\Bus\Product\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as VIC;

class GetImagesQuery extends Message
{
    /**
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $baseProductId;

    /**
     * @VIC\Enum("AppBundle\Enum\BaseProductImage")
     */
    public $size;
}
