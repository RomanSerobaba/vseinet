<?php 

namespace PromoBundle\Bus\ProductReview\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class EditCommentCommand extends Message
{
    /**
     * @VIA\Description("Отзыв id")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Комментарий")
     * @Assert\NotBlank(message="Не указан комментарий")
     * @Assert\Type(type="string")
     */
    public $answer;
}