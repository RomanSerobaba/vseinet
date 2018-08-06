<?php 

namespace PromoBundle\Bus\ProductReview\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class DeleteProductReviewCommand extends Message
{
    /**
     * @VIA\Description("Отзыв id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $id;
}