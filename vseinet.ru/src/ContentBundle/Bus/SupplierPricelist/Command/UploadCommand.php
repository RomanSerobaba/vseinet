<?php 

namespace ContentBundle\Bus\SupplierPricelist\Command;

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
     * @Assert\NotBlank(message="Загрузите файл Excel (xls ли xlsx)")
     * @Assert\File(
     *     mimeTypes={
     *         "application/vnd.ms-office", 
     *         "application/vnd.ms-excel", 
     *         "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
     *         "application/CDFV2-unknown"
     *     }
     * )
     */
    public $pricelist;
}