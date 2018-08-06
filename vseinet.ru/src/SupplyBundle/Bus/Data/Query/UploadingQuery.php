<?php 

namespace SupplyBundle\Bus\Data\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class UploadingQuery extends Message
{
    /**
     * @Assert\NotBlank(message="Value of 'id' should not be blank")
     * @VIA\Description("Supply id")
     */
    public $id;

    /**
     * @VIA\Description("Файл загрузки")
     * @Assert\NotBlank(message="Value of 'filename' should not be blank")
     * @Assert\File(
     *     mimeTypes={
     *         "text/csv",
     *         "text/plain",
     *         "application/octet-stream",
     *         "application/vnd.ms-office",
     *         "application/vnd.ms-excel",
     *         "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
     *     }
     * )
     */
    public $filename;
}