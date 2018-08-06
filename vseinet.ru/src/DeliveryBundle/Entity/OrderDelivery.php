<?php

namespace DeliveryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderDelivery
 *
 * @ORM\Table(name="order_delivery")
 * @ORM\Entity(repositoryClass="DeliveryBundle\Repository\OrderDeliveryRepository")
 */
class OrderDelivery
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_address_id", type="integer")
     */
    private $geoAddressId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="comment", type="string", length=255, nullable=true)
     */
    private $comment;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var int|null
     *
     * @ORM\Column(name="created_by", type="integer", nullable=true)
     */
    private $createdBy;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="desired_datetime", type="datetime", nullable=true)
     */
    private $desiredDatetime;

    /**
     * @var int
     *
     * @ORM\Column(name="cost", type="integer")
     */
    private $cost;

    /**
     * @var bool
     *
     * @ORM\Column(name="need_lifting", type="boolean")
     */
    private $needLifting;

    /**
     * @var int
     *
     * @ORM\Column(name="lifting_cost", type="integer")
     */
    private $liftingCost;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var int|null
     *
     * @ORM\Column(name="freight_operator_id", type="integer", nullable=true)
     */
    private $freightOperatorId;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set geoAddressId.
     *
     * @param int $geoAddressId
     *
     * @return OrderDelivery
     */
    public function setGeoAddressId($geoAddressId)
    {
        $this->geoAddressId = $geoAddressId;

        return $this;
    }

    /**
     * Get geoAddressId.
     *
     * @return int
     */
    public function getGeoAddressId()
    {
        return $this->geoAddressId;
    }

    /**
     * Set comment.
     *
     * @param string|null $comment
     *
     * @return OrderDelivery
     */
    public function setComment($comment = null)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment.
     *
     * @return string|null
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return OrderDelivery
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set createdBy.
     *
     * @param int|null $createdBy
     *
     * @return OrderDelivery
     */
    public function setCreatedBy($createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy.
     *
     * @return int|null
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set desiredDatetime.
     *
     * @param \DateTime|null $desiredDatetime
     *
     * @return OrderDelivery
     */
    public function setDesiredDatetime($desiredDatetime = null)
    {
        $this->desiredDatetime = $desiredDatetime;

        return $this;
    }

    /**
     * Get desiredDatetime.
     *
     * @return \DateTime|null
     */
    public function getDesiredDatetime()
    {
        return $this->desiredDatetime;
    }

    /**
     * Set cost.
     *
     * @param int $cost
     *
     * @return OrderDelivery
     */
    public function setCost($cost)
    {
        $this->cost = $cost;

        return $this;
    }

    /**
     * Get cost.
     *
     * @return int
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * Set needLifting.
     *
     * @param bool $needLifting
     *
     * @return OrderDelivery
     */
    public function setNeedLifting($needLifting)
    {
        $this->needLifting = $needLifting;

        return $this;
    }

    /**
     * Get needLifting.
     *
     * @return bool
     */
    public function getNeedLifting()
    {
        return $this->needLifting;
    }

    /**
     * Set liftingCost.
     *
     * @param int $liftingCost
     *
     * @return OrderDelivery
     */
    public function setLiftingCost($liftingCost)
    {
        $this->liftingCost = $liftingCost;

        return $this;
    }

    /**
     * Get liftingCost.
     *
     * @return int
     */
    public function getLiftingCost()
    {
        return $this->liftingCost;
    }

    /**
     * Set type.
     *
     * @param string $type
     *
     * @return OrderDelivery
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set freightOperatorId.
     *
     * @param int|null $freightOperatorId
     *
     * @return OrderDelivery
     */
    public function setFreightOperatorId($freightOperatorId = null)
    {
        $this->freightOperatorId = $freightOperatorId;

        return $this;
    }

    /**
     * Get freightOperatorId.
     *
     * @return int|null
     */
    public function getFreightOperatorId()
    {
        return $this->freightOperatorId;
    }
}
