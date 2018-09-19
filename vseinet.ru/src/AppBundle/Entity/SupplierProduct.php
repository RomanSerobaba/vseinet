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

