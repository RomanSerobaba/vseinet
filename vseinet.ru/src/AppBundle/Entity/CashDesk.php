<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CacheDesk.
 *
 * @ORM\Table(name="cash_desk")
 * @ORM\Entity
 */
class CashDesk
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="financial_resource_id_seq", initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string")
     */
    private $title;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     *
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @ORM\Column(name="deactivated_at", type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    private $deactivatedAt;

    /**
     * @ORM\Column(name="our_seller_id", type="integer", nullable=false)
     *
     * @var int
     */
    private $ourSellerId;

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
     * Set title.
     *
     * @param string $title
     *
     * @return self
     */
    public function setTitle($title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set type.
     *
     * @param string $type
     *
     * @return self
     */
    public function setType($type): self
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
     * Set createdAt.
     *
     * @param \DateTime|null $createdAt
     *
     * @return self
     */
    public function setCreatedAt(?\DateTime $createdAt = null): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime|null
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * Set deactivatedAt.
     *
     * @param \DateTime|null $deactivatedAt
     *
     * @return self
     */
    public function setDeactivatedAt(?\DateTime $deactivatedAt = null): self
    {
        $this->deactivatedAt = $deactivatedAt;

        return $this;
    }

    /**
     * Get deactivatedAt.
     *
     * @return \DateTime|null
     */
    public function getDeactivatedAt(): ?\DateTime
    {
        return $this->deactivatedAt;
    }

    /**
     * @param int $ourSellerId
     *
     * @return self
     */
    public function setOurSellerId(int $ourSellerId): self
    {
        $this->ourSellerId = $ourSellerId;

        return $this;
    }

    /**
     * @return int
     */
    public function getOurSellerId(): int
    {
        return $this->ourSellerId;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string")
     */
    public $type = 'cashDesk';

    /**
     * @var string
     *
     * @ORM\Column(name="reg_number", type="string", length=100)
     */
    public $regNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="ip_address", type="string", length=100)
     */
    public $ipAddress;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_room_id", type="integer")
     */
    public $geoRoomId;

    /**
     * @var int
     *
     * @ORM\Column(name="collector_id", type="integer")
     */
    public $collectorId;

    /**
     * @var int
     *
     * @ORM\Column(name="cash_desk_of_encashment_id", type="integer", nullable=true)
     */
    public $cashDeskOfEncashmentId;

    /**
     * @var int
     *
     * @ORM\Column(name="org_department_id", type="integer")
     */
    public $orgDepartmentId;

    /**
     * @var int
     *
     * @ORM\Column(name="terminal_id", type="integer", nullable=true)
     */
    public $terminalId;

    /**
     * Set reg number.
     *
     * @param string $regNumber
     *
     * @return self
     */
    public function setRegNumber($regNumber)
    {
        $this->regNumber = $regNumber;

        return $this;
    }

    /**
     * Get reg number.
     *
     * @return string
     */
    public function getRegNumber()
    {
        return $this->regNumber;
    }

    /**
     * Set ipAddress.
     *
     * @param string $ipAddress
     *
     * @return self
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    /**
     * Get ipAddress.
     *
     * @return string
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * Set geoRoomId.
     *
     * @param int $geoRoomId
     *
     * @return self
     */
    public function setGeoRoomId($geoRoomId)
    {
        $this->geoRoomId = $geoRoomId;

        return $this;
    }

    /**
     * Get geoRoomId.
     *
     * @return int
     */
    public function getGeoRoomId()
    {
        return $this->geoRoomId;
    }

    /**
     * Set collectorId.
     *
     * @param int $collectorId
     *
     * @return self
     */
    public function setCollectorId($collectorId)
    {
        $this->collectorId = $collectorId;

        return $this;
    }

    /**
     * Get collectorId.
     *
     * @return int
     */
    public function getCollectorId()
    {
        return $this->collectorId;
    }

    /**
     * Set cashDeskCollectionId.
     *
     * @param int $cashDeskOfEncashmentId
     *
     * @return self
     */
    public function setCashDeskOfEncashmentId($cashDeskOfEncashmentId)
    {
        $this->cashDeskOfEncashmentId = $cashDeskOfEncashmentId;

        return $this;
    }

    /**
     * Get cashDeskCollectionId.
     *
     * @return int
     */
    public function getCashDeskOfEncashmentId()
    {
        return $this->cashDeskOfEncashmentId;
    }

    /**
     * Set orgDepartmentId.
     *
     * @param int $orgDepartmentId
     *
     * @return self
     */
    public function setOrgDepartmentId($orgDepartmentId)
    {
        $this->orgDepartmentId = $orgDepartmentId;

        return $this;
    }

    /**
     * Get orgDepartmentId.
     *
     * @return int
     */
    public function getOrgDepartmentId()
    {
        return $this->orgDepartmentId;
    }

    /**
     * Set terminalId.
     *
     * @param int $terminalId
     *
     * @return self
     */
    public function setTerminalId($terminalId)
    {
        $this->terminalId = $terminalId;

        return $this;
    }

    /**
     * Get terminalId.
     *
     * @return int
     */
    public function getTerminalId()
    {
        return $this->terminalId;
    }
}
