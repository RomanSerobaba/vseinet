<?php

namespace ReservesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AvailableGoodsReservation
 *
 * @ORM\Table(name="available_goods_reservation")
 * @ORM\Entity(repositoryClass="ReservesBundle\Repository\AvailableGoodsReservationRepository")
 */
class AvailableGoodsReservation
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
     * @var string|null
     *
     * @ORM\Column(name="parent_doc_type", type="string", length=255, nullable=true)
     */
    private $parentDocType;

    /**
     * @var int|null
     *
     * @ORM\Column(name="parent_doc_id", type="integer", nullable=true)
     */
    private $parentDocId;


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
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return AvailableGoodsReservation
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
     * @return AvailableGoodsReservation
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
     * Set parentDocType.
     *
     * @param string|null $parentDocType
     *
     * @return AvailableGoodsReservation
     */
    public function setParentDocType($parentDocType = null)
    {
        $this->parentDocType = $parentDocType;

        return $this;
    }

    /**
     * Get parentDocType.
     *
     * @return string|null
     */
    public function getParentDocType()
    {
        return $this->parentDocType;
    }

    /**
     * Set parentDocId.
     *
     * @param int|null $parentDocId
     *
     * @return AvailableGoodsReservation
     */
    public function setParentDocId($parentDocId = null)
    {
        $this->parentDocId = $parentDocId;

        return $this;
    }

    /**
     * Get parentDocId.
     *
     * @return int|null
     */
    public function getParentDocId()
    {
        return $this->parentDocId;
    }
}
