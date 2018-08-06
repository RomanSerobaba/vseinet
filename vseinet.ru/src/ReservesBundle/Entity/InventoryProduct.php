<?php

namespace ReservesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * InventoryProduct
 *
 * @ORM\Table(name="inventory_product")
 * @ORM\Entity(repositoryClass="ReservesBundle\Repository\InventoryProductRepository")
 */
class InventoryProduct
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
     * @ORM\Column(name="initial_quantity", type="integer")
     */
    private $initialQuantity;
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Методы">    
    
    /**
     * Get inventoryId
     *
     * @return integer
     */
    public function getInventoryDId()
    {
        return $this->inventoryDId;
    }

    /**
     * Set inventoryId
     *
     * @param integer $inventoryId
     *
     * @return InventoryProduct
     */
    public function setInventoryDId($inventoryId)
    {
        $this->inventoryDId = $inventoryId;

        return $this;
    }

    /**
     * Get baseProductId
     *
     * @return integer
     */
    public function getBaseProductId()
    {
        return $this->baseProductId;
    }

    /**
     * Set baseProductId
     *
     * @param integer $baseProductId
     *
     * @return InventoryProduct
     */
    public function setBaseProductId($baseProductId)
    {
        $this->baseProductId = $baseProductId;

        return $this;
    }

    /**
     * Get initialQuantity
     *
     * @return integer
     */
    public function getInitialQuantity()
    {
        return $this->initialQuantity;
    }

    /**
     * Set initialQuantity
     *
     * @param integer $initialQuantity
     *
     * @return InventoryProduct
     */
    public function setInitialQuantity($initialQuantity)
    {
        $this->initialQuantity = $initialQuantity;

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
