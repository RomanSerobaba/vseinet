<?php 

namespace SupplyBundle\Bus\Data\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class CloseSupplierInvoiceCommand extends Message
{
    /**
     * @VIA\Description("Supplier invoice id")
     * @Assert\NotBlank(message="Значение id не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Дата прихода")
     * @Assert\NotBlank(message="Значение даты не должно быть пустым")
     * @Assert\Type(type="datetime")
     */
    public $arrivingTime;

    /**
     * @VIA\Description("Контрагент поставщика")
     * @Assert\NotBlank(message="Значение контрагент поставщика не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $supplierCounteragentId;

    /**
     * @VIA\Description("Коментарий")
     * @Assert\Type(type="string")
     */
    public $comment;
}