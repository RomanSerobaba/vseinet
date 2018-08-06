<?php

namespace OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GoodsRequestAnnul
 *
 * @ORM\Table(name="goods_request_annul")
 * @ORM\Entity(repositoryClass="OrderBundle\Repository\GoodsRequestAnnulRepository")
 */
class GoodsRequestAnnul
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
     * @ORM\Column(name="goods_request_id", type="integer")
     */
    private $goodsRequestId;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var integer|null
     *
     * @ORM\Column(name="created_by", type="string", nullable=true)
     */
    private $createdBy;


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
     * Set goodsRequestId.
     *
     * @param int $goodsRequestId
     *
     * @return GoodsRequestAnnul
     */
    public function setGoodsRequestId($goodsRequestId)
    {
        $this->goodsRequestId = $goodsRequestId;

        return $this;
    }

    /**
     * Get goodsRequestId.
     *
     * @return int
     */
    public function getGoodsRequestId()
    {
        return $this->goodsRequestId;
    }

    /**
     * Set quantity.
     *
     * @param integer $quantity
     *
     * @return GoodsRequestAnnul
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity.
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return GoodsRequestAnnul
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
     * @param integer|null $createdBy
     *
     * @return GoodsRequestAnnul
     */
    public function setCreatedBy($createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy.
     *
     * @return integer|null
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }
}
