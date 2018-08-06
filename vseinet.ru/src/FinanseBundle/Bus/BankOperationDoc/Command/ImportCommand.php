<?php 

namespace FinanseBundle\Bus\BankOperationDoc\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class ImportCommand extends Message
{    

    /**
     * @Assert\NotBlank(message="Файл банковской выписки не загружен")
     * @Assert\File(
     *     mimeTypes={"text/plain"}
     * )
     */
    public $uploadFile;
    
    /**
     * @Assert\Uuid
     */
    public $uuid;

//     * @Assert\NotBlank(message="Файл не загружен")
    
}