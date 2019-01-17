<?php

namespace AppBundle\Bus\Order\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Bus\User\Query\DTO\Contact;
use AppBundle\Enum\OrderItemStatus;

class TransportCompany
{
    /**
     * @Assert\Type(type="integer", message="Идентификатор транспортной компании должен быть числом")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="integer", message="Стоимость доставки до транспортной компании должна быть числом")
     */
    public $deliveryTax;

    /**
     * @Assert\Type(type="string")
     */
    public $url;

    public function __construct($id, $name, $deliveryTax, $url)
    {
        $this->id = $id;
        $this->name = $name;
        $this->deliveryTax = $deliveryTax;
        $this->url = $url;
    }
}
