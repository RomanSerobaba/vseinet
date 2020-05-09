<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SupplierProduct
 *
 * @ORM\Table(name="supplier_product")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SupplierProductRepository")
 */
class SupplierProduct
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
     * @ORM\Column(name="base_product_id", type="integer")
     */
    private $baseProductId;

    /**
     * @var int
     *
     * @ORM\Column(name="supplier_id", type="integer")
     */
    private $supplierId;

    /**
     * @var int
     *
     * @ORM\Column(name="partner_product_id", type="integer")
     */
    private $partnerProductId;

    /**
     * @var string
     *
     * @ORM\Column(name="product_availability_code", type="string")
     */
    private $productAvailabilityCode;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set baseProductId
     *
     * @param integer $baseProductId
     *
     * @return SupplierProduct
     */
    public function setBaseProductId($baseProductId)
    {
        $this->baseProductId = $baseProductId;

        return $this;
    }

    /**
     * Get baseProductId
     *
     * @return int
     */
    public function getBaseProductId()
    {
        return $this->baseProductId;
    }

    /**
     * Set supplierId
     *
     * @param integer $supplierId
     *
     * @return SupplierProduct
     */
    public function setSupplierId($supplierId)
    {
        $this->supplierId = $supplierId;

        return $this;
    }

    /**
     * Get supplierId
     *
     * @return int
     */
    public function getSupplierId()
    {
        return $this->supplierId;
    }

    /**
     * Set partnerProductId
     *
     * @param integer $partnerProductId
     *
     * @return SupplierProduct
     */
    public function setPartnerProductId($partnerProductId)
    {
        $this->partnerProductId = $partnerProductId;

        return $this;
    }

    /**
     * Get partnerProductId
     *
     * @return int
     */
    public function getPartnerProductId()
    {
        return $this->partnerProductId;
    }

    /**
     * Set productAvailabilityCode
     *
     * @param string $productAvailabilityCode
     *
     * @return SupplierProduct
     */
    public function setProductAvailabilityCode($productAvailabilityCode)
    {
        $this->productAvailabilityCode = $productAvailabilityCode;

        return $this;
    }

    /**
     * Get productAvailabilityCode
     *
     * @return string
     */
    public function getProductAvailabilityCode()
    {
        return $this->productAvailabilityCode;
    }
}

