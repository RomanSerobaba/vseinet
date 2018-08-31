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
}
