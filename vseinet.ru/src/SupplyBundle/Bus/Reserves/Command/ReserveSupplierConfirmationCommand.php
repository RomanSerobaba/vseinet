<?php 

namespace SupplyBundle\Bus\Reserves\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class ReserveSupplierConfirmationCommand extends Message
{
    /**
     * @Assert\Type(type="integer")
     * @Assert\NotBlank(message="Значение id не должно быть пустым")
     * @VIA\Description("Supplier reserve id")
     */
    public $id;

    /**
     * @Assert\Type(type="integer")
     * @Assert\NotBlank(message="Значение orderItemId не должно быть пустым")
     * @VIA\Description("Order item id")
     */
    public $orderItemId;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("New quantity")
     * @VIA\DefaultValue(0)
     */
    public $newQuantity;

    /**
     * @Assert\Type(type="float")
     * @Assert\NotBlank(message="Значение newPurchasePrice не должно быть пустым")
     * @VIA\Description("New purchase price")
     * @VIA\DefaultValue(0)
     */
    public $newPurchasePrice;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Old quantity")
     * @VIA\DefaultValue(0)
     */
    public $oldQuantity;

    /**
     * @Assert\Type(type="float")
     * @Assert\NotBlank(message="Значение oldPurchasePrice не должно быть пустым")
     * @VIA\Description("Old purchase price")
     * @VIA\DefaultValue(0)
     */
    public $oldPurchasePrice;
}