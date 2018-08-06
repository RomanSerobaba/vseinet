<?php

namespace SupplyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SupplierProductBarCode
 *
 * @ORM\Table(name="supplier_product_bar_code")
 * @ORM\Entity(repositoryClass="SupplyBundle\Repository\SupplierProductBarCodeRepository")
 */
class SupplierProductBarCode
{
    /**
     * @var int
     *
     * @ORM\Column(name="supplier_product_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $productId;

    /**
     * @var string
     *
     * @ORM\Column(name="bar_code", type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $barCode;


    /**
     * Set productId
     *
     * @param integer $productId
     *
     * @return SupplierProductBarCode
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;

        return $this;
    }

    /**
     * Get productId
     *
     * @return int
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * Set barCode
     *
     * @param string $barCode
     *
     * @return SupplierProductBarCode
     */
    public function setBarCode($barCode)
    {
        $this->barCode = $barCode;

        return $this;
    }

    /**
     * Get barCode
     *
     * @return string
     */
    public function getBarCode()
    {
        return $this->barCode;
    }
}

