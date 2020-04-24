<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderDoc
 *
 * @ORM\Table(name="order_doc")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OrderDocRepository")
 */
class OrderDoc
{
    /**
     * @var int
     * @ORM\Column(name="did", type="integer")
     * @ORM\Id
     */
    private $DId;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_point_id", type="integer")
     */
    public $geoPointId;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_city_id", type="integer")
     */
    public $geoCityId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="order_type_code", type="string", length=30)
     */
    public $orderTypeCode;

    /**
     * @var int
     *
     * @ORM\Column(name="number", type="integer")
     */
    private $number;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_cancel_requested", type="boolean", nullable=true)
     */
    private $isCancelRequested;


    /**
     * Get DId.
     *
     * @return int
     */
    public function getDId()
    {
        return $this->DId;
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
     * Get number.
     *
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set isCancelRequested.
     *
     * @param bool|null $isCancelRequested
     *
     * @return Bank
     */
    public function setIsCancelRequested($isCancelRequested = null)
    {
        $this->isCancelRequested = $isCancelRequested;

        return $this;
    }

    /**
     * Get isCancelRequested.
     *
     * @return bool|null
     */
    public function getIsCancelRequested()
    {
        return $this->isCancelRequested;
    }

    /**
     * Set orderTypeCode.
     *
     * @param string $orderTypeCode
     *
     * @return OrderDoc
     */
    public function setOrderTypeCode($orderTypeCode)
    {
        $this->orderTypeCode = $orderTypeCode;

        return $this;
    }

    /**
     * Get orderTypeCode.
     *
     * @return string
     */
    public function getOrderTypeCode()
    {
        return $this->orderTypeCode;
    }

    /**
     * Set geoPointId.
     *
     * @param int $geoPointId
     *
     * @return OrderDoc
     */
    public function setGeoPointId($geoPointId)
    {
        $this->geoPointId = $geoPointId;

        return $this;
    }

    /**
     * Get geoPointId.
     *
     * @return int
     */
    public function getGeoPointId()
    {
        return $this->geoPointId;
    }

    /**
     * Set geoCityId.
     *
     * @param int $geoCityId
     *
     * @return OrderDoc
     */
    public function setGeoCityId($geoCityId)
    {
        $this->geoCityId = $geoCityId;

        return $this;
    }

    /**
     * Get geoCityId.
     *
     * @return int
     */
    public function getGeoCityId()
    {
        return $this->geoCityId;
    }
}
