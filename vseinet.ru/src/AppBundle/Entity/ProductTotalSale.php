<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductTotalSale.
 *
 * @ORM\Table(name="product_total_sale")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductTotalSaleRepository")
 */
class ProductTotalSale
{
    /**
     * @var int
     *
     * @ORM\Column(name="base_product_id", type="integer")
     * @ORM\Id
     */
    private $baseProductId;

    /**
     * Set baseProductId.
     *
     * @param int $baseProductId
     *
     * @return ProductTotalSale
     */
    public function setBaseProductId($baseProductId)
    {
        $this->baseProductId = $baseProductId;

        return $this;
    }

    /**
     * Get baseProductId.
     *
     * @return int
     */
    public function getBaseProductId()
    {
        return $this->baseProductId;
    }
}
