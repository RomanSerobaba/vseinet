<?php 

namespace SupplyBundle\Bus\Reserves\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class UploadQuery extends Message
{
    /**
     * @Assert\NotBlank(message="Value of 'id' should not be blank")
     * @VIA\Description("Поставщик")
     */
    public $id;

    /**
     * @VIA\Description("Точка")
     * @VIA\DefaultValue("0")
     */
    public $pointId;

    /**
      * @VIA\Description("С подтвержденными резервами")
     */
    public $withConfirmedReserves;

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