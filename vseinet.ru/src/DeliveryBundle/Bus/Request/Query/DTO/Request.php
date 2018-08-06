<?php 

namespace DeliveryBundle\Bus\Request\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Request
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $title;

    /**
     * @Assert\Type(type="string")
     */
    public $address;

    /**
     * @Assert\Type(type="string")
     */
    public $client;

    /**
     * @Assert\Type(type="string")
     */
    public $city;

    /**
     * @Assert\Type(type="boolean")
     */
    public $needLifting;

    /**
     * @Assert\Type(type="datetime")
     */
    public $desiredDateTime;

    /**
     * @Assert\Type(type="integer")
     */
    public $cost;

    /**
     * @Assert\Type(type="integer")
     */
    public $liftingCost;
    
    /**
     * @Assert\Type(type="array<DeliveryBundle\Bus\Request\Query\DTO\RequestItem>")
     */
    public $items;

    public function __construct($id, $title, $address, $client, $city, $needLifting, $desiredDateTime, $cost = 0, $liftingCost = 0, $items = [])
    {
        $this->id = $id;
        $this->title = $title;
        $this->address = $address;
        $this->client = $client;
        $this->city = $city;
        $this->needLifting = $needLifting;
        $this->desiredDateTime = $desiredDateTime;
        $this->cost = $liftingCost;
        $this->items = $items;
    }
}