<?php 

namespace ContentBundle\Bus\SupplierPricelist\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class LoadCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Значение id не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\NotBlank(message="Файл не загружен")
     * @Assert\File(
     *     mimeTypes={
     *         "application/vnd.ms-office", 
     *         "application/vnd.ms-excel", 
     *         "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
     *         "application/CDFV2-unknown"
     *     }
     * )
     */
    public $filename;
}