<?php

namespace DeliveryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DeliveryDoc
 *
 * @ORM\Table(name="delivery_doc")
 * @ORM\Entity(repositoryClass="DeliveryBundle\Repository\DeliveryDocRepository")
 */
class DeliveryDoc
{
    use \DocumentBundle\Prototipe\DocumentEntity;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_point_id", type="integer", nullable=true)
     */
    private $geoPointId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="courier_id", type="integer", nullable=true)
     */
    private $courierId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="transport_company_id", type="integer", nullable=true)
     */
    private $transportCompanyId;

    /**
     * Set type.
     *
     * @param string $type
     *
     * @return DeliveryDoc
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
     * Set date.
     *
     * @param \DateTime $date
     *
     * @return DeliveryDoc
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set geoPointId.
     *
     * @param int $geoPointId
     *
     * @return DeliveryDoc
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
     * Set courierId.
     *
     * @param int|null $courierId
     *
     * @return DeliveryDoc
     */
    public function setCourierId($courierId = null)
    {
        $this->courierId = $courierId;

        return $this;
    }

    /**
     * Get courierId.
     *
     * @return int|null
     */
    public function getCourierId()
    {
        return $this->courierId;
    }

    /**
     * Set transportCompanyId.
     *
     * @param int|null $transportCompanyId
     *
     * @return DeliveryDoc
     */
    public function setTransportCompanyId($transportCompanyId = null)
    {
        $this->transportCompanyId = $transportCompanyId;

        return $this;
    }

    /**
     * Get transportCompanyId.
     *
     * @return int|null
     */
    public function getTransportCompanyId()
    {
        return $this->transportCompanyId;
    }
}
