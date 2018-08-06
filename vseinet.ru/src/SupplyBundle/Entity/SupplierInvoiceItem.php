<?php

namespace SupplyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SupplierInvoiceItem
 *
 * @ORM\Table(name="supplier_invoice_item")
 * @ORM\Entity(repositoryClass="SupplyBundle\Repository\SupplierInvoiceItemRepository")
 */
class SupplierInvoiceItem
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
     * @ORM\Column(name="supplier_invoice_id", type="integer")
     */
    private $supplierInvoiceId;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var int|null
     *
     * @ORM\Column(name="base_product_id", type="integer", nullable=true)
     */
    private $baseProductId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="price", type="integer", nullable=true)
     */
    private $price;


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
     * Set supplierInvoiceId.
     *
     * @param int $supplierInvoiceId
     *
     * @return SupplierInvoiceItem
     */
    public function setSupplierInvoiceId($supplierInvoiceId)
    {
        $this->supplierInvoiceId = $supplierInvoiceId;

        return $this;
    }

    /**
     * Get supplierInvoiceId.
     *
     * @return int
     */
    public function getSupplierInvoiceId()
    {
        return $this->supplierInvoiceId;
    }

    /**
     * Set quantity.
     *
     * @param int $quantity
     *
     * @return SupplierInvoiceItem
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity.
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set baseProductId.
     *
     * @param int|null $baseProductId
     *
     * @return SupplierInvoiceItem
     */
    public function setBaseProductId($baseProductId = null)
    {
        $this->baseProductId = $baseProductId;

        return $this;
    }

    /**
     * Get baseProductId.
     *
     * @return int|null
     */
    public function getBaseProductId()
    {
        return $this->baseProductId;
    }

    /**
     * Set price.
     *
     * @param int|null $price
     *
     * @return SupplierInvoiceItem
     */
    public function setPrice($price = null)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price.
     *
     * @return int|null
     */
    public function getPrice()
    {
        return $this->price;
    }
}
