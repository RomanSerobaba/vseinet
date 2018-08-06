<?php 

namespace ContentBundle\Bus\BaseProductImage\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class SortCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Значение id не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\NotBlank(message="Значение underId не должно быть пустым")
     * @Assert\Type(type="integer")
     * @VIA\Description("Изображение, за которое перемещается, 0, чтобы установить первым")
     */
    public $underId;
}