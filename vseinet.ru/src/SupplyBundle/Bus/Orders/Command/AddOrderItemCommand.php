<?php 

namespace SupplyBundle\Bus\Orders\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class AddOrderItemCommand extends Message
{
    /**
     * @VIA\Description("Order id")
     * @Assert\NotBlank(message="Идентифкатор заказа не должен быть пустым")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Quantity")
     * @Assert\NotBlank(message="Количество не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $quantity;

    /**
     * @VIA\Description("Base product id")
     * @Assert\NotBlank(message="Идентифкатор товара не должен быть пустым")
     * @Assert\Type(type="integer")
     */
    public $baseProductId;

    /**
     * @VIA\Description("User id")
     * @Assert\NotBlank(message="Идентифкатор пользователя не должен быть пустым")
     * @Assert\Type(type="integer")
     */
    public $userId;

    /**
     * @Assert\Uuid
     */
    public $uuid;
}