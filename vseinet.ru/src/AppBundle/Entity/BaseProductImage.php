<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BaseProductImage.
 *
 * @ORM\Table(name="base_product_image")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BaseProductImageRepository")
 */
class BaseProductImage
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
     * @ORM\Column(name="basename", type="string")
     */
    private $basename;

    /**
     * @var int
     *
     * @ORM\Column(name="width", type="integer")
     */
    private $width;

    /**
     * @var int
     *
     * @ORM\Column(name="height", type="integer")
     */
    private $height;

    /**
     * @var int
     *
     * @ORM\Column(name="sort_order", type="integer")
     */
    private $sortOrder;

    /**
     * @Assert\Type(type="string")
     */
    public $src;

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
     * Set baseProductId.
     *
     * @param int $baseProductId
     *
     * @return ProductImage
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

    /**
     * Set basename.
     *
     * @param string $basename
     *
     * @return BaseProductImage
     */
    public function setBasename($basename)
    {
        $this->basename = $basename;

        return $this;
    }

    /**
     * Get basename.
     *
     * @return string
     */
    public function getBasename()
    {
        return $this->basename;
    }

    /**
     * Set width.
     *
     * @param int $width
     *
     * @return BaseProductImage
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get width.
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set height.
     *
     * @param int $height
     *
     * @return BaseProductImage
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get height.
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set sortOrder.
     *
     * @param int $sortOrder
     *
     * @return BaseProductImage
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    /**
     * Get sortOrder.
     *
     * @return int
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }
}
