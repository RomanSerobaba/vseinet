<?php 

namespace ContentBundle\Bus\BrandLogo\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class UploadCommand extends Message 
{
    /**
     * @Assert\NotBlank(message="Значение id не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\NotBlank(message="Загрузите файл изображения")
     * @Assert\File(
     *     mimeTypes={
     *         "image/jpeg", 
     *         "image/png" 
     *     }
     * )
     */
    public $logo;
}