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
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

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
}
