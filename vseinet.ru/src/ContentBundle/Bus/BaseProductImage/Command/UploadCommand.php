<?php 

namespace ContentBundle\Bus\BaseProductImage\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class UploadCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Значение baseProductId не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $baseProductId;

    /**
     * @Assert\NotBlank(message="Значение image не должно быть пустым")
     * @Assert\Image
     */
    public $image;

    /**
     * @Assert\Uuid
     */
    public $uuid;
}