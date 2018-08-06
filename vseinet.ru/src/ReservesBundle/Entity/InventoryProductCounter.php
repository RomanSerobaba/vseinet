<?php

namespace ReservesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
//use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * InventoryProductCounter
 *
 * @ORM\Table(name="inventory_product_counter")
 * @ORM\Entity(repositoryClass="ReservesBundle\Repository\InventoryProductCounterRepository")
 */
class InventoryProductCounter
{
    
    // <editor-fold defaultstate="collapsed" desc="Поля">    
    
    /**
     * @var integer
     * @ORM\Column(name="inventory_did", type="integer")
     * @ORM\Id
     */
    private $inventoryDId;

    /**
     * @var integer
     * @ORM\Column(name="base_product_id", type="integer")
     * @ORM\Id
     */
    private $baseProductId;
    
    /**
     * @var integer
     * @ORM\Column(name="participant_id", type="integer")
     * @ORM\Id
     */
    private $participantId;
    
    /**
     * @var integer
     * @ORM\Column(name="found_quantity", type="integer", nullable=true)
     */
    private $foundQuantity;
    
    /**
     * @var string
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;
    
    /**
     * @var \DateTime
     * @ORM\Column(name="update_at", type="datetime")
     */
    private $updateAt;
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Методы">    
    
    /**
     * Get inventoryDId
     *
     * @return integer
     */
    public function getInventoryDId()
    {
        return $this->inventoryDId;
    }

    /**
     * Set inventoryDId
     *
     * @param integer $inventoryDId
     *
     * @return InventoryProductCounter
     */
    public function setInventoryDId($inventoryDId)
    {
        $this->inventoryDId = $inventoryDId;

        return $this;
    }

    /**
     * Get baseProductId
     *
     * @return integer
     */
    public function getBaseProductId()
    {
        return $this->participantId;
    }

    /**
     * Set baseProductId
     *
     * @param integer $baseProductId
     *
     * @return InventoryProductCounter
     */
    public function setBaseProductId($baseProductId)
    {
        $this->baseProductId = $baseProductId;

        return $this;
    }

    /**
     * Get participantId
     *
     * @return integer
     */
    public function getParticipantId()
    {
        return $this->participantId;
    }

    /**
     * Set participantId
     *
     * @param integer $participantId
     *
     * @return InventoryProductCounter
     */
    public function setParticipantId($participantId)
    {
        $this->participantId = $participantId;

        return $this;
    }

    /**
     * Get foundQuantity
     *
     * @return integer
     */
    public function getFoundQuantity()
    {
        return $this->foundQuantity;
    }

    /**
     * Set foundQuantity
     *
     * @param integer $foundQuantity
     *
     * @return InventoryProductCounter
     */
    public function setFoundQuantity($foundQuantity)
    {
        $this->foundQuantity = $foundQuantity;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set comment
     *
     * @param integer $text
     *
     * @return InventoryProductCounter
     */
    public function setComment($text)
    {
        $this->comment = $text;

        return $this;
    }

    /**
     * Get updateAt
     *
     * @return \DateTime
     */
    public function getUpdateAt()
    {
        return $this->updateAt;
    }

    /**
     * Set updateAt
     *
     * @param \DateTime $updateAt
     *
     * @return InventoryProductCounter
     */
    public function setUpdateAt($updateAt)
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    /**
     * Заполнение полей
     * 
     * @param array $data
     */
    public function fill($data)
    {
        foreach ($data as $key => $value) {
            $method = 'set'. ucfirst($key);
            $this->$method($value);
        }
    }
        
    // </editor-fold>
}
