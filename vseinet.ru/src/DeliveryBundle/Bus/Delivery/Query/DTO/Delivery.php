<?php 

namespace DeliveryBundle\Bus\Delivery\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Delivery
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="integer")
     */
    public $number;

    /**
     * @Assert\Type(type="integer")
     */
    public $pointId;

    /**
     * @Assert\Type(type="integer")
     */
    public $courierId;

    /**
     * @Assert\Type(type="integer")
     */
    public $transportCompanyId;

    /**
     * @Assert\Type(type="string")
     */
    public $title;

    /**
     * @Assert\Type(type="string")
     */
    public $type;

    /**
     * @Assert\Type(type="string")
     */
    public $statusCode;

    /**
     * @Assert\Type(type="date")
     */
    public $date;

    /**
     * @Assert\Type(type="integer")
     */
    public $requestsCount;

    /**
     * @Assert\Type(type="datetime")
     */
    public $shippedAt;

    /**
     * @Assert\Type(type="datetime")
     */
    public $completedAt;

    public function __construct($id, $number, $pointId, $courierId, $transportCompanyId, $title, $type, $statusCode, $date, $requestsCount = 0, $shippedAt = null, $completedAt = null)
    {
        $this->id = $id;
        $this->number = $number;
        $this->title = $title;
        $this->type = $type;
        $this->pointId = $pointId;
        $this->courierId = $courierId;
        $this->transportCompanyId = $transportCompanyId;
        $this->date = $date;
        $this->requestsCount = $requestsCount;
        $this->shippedAt = $shippedAt;
        $this->completedAt = $completedAt;
        $this->statusCode = $statusCode;
    }
}