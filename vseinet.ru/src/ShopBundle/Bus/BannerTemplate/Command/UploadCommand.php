<?php 

namespace ShopBundle\Bus\BannerTemplate\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class UploadCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Загрузите файл изображения")
     * @Assert\File(
     *     mimeTypes={
     *         "image/jpeg",
     *         "image/png"
     *     }
     * )
     */
    public $file;

    /**
     * @VIA\Description("Имя файла (не передавать)")
     * @Assert\Type(type="string")
     */
    public $filename;
}