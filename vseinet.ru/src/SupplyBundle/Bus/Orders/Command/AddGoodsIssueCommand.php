<?php 

namespace SupplyBundle\Bus\Orders\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class AddGoodsIssueCommand extends Message
{
    /**
     * @VIA\Description("User id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $userId;

    /**
     * @VIA\Description("Order item id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $orderItemId;

    /**
     * @VIA\Description("Product resort id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $productResortId;

    /**
     * @VIA\Description("Код")
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    public $code;

    /**
     * @VIA\Description("Описание")
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    public $description;
}