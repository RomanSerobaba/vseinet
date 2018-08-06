<?php 

namespace ShopBundle\Bus\Banner\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GetBannerQuery extends Message
{
    /**
     * @VIA\Description("Баннер id")
     * @Assert\NotBlank(message="Баннер не указан")
     * @Assert\Type(type="integer")
     */
    public $id;
}