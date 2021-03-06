<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Category.
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategoryRepository")
 */
class Category
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
     * @ORM\Column(name="pid", type="integer", nullable=true)
     */
    private $pid;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="alias_for_id", type="integer", nullable=true)
     */
    private $aliasForId;

    /**
     * @var bool
     *
     * @ORM\Column(name="use_exname", type="boolean")
     */
    private $useExname;

    /**
     * @var string
     *
     * @ORM\Column(name="tpl", type="string")
     */
    private $tpl;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_tpl_enabled", type="boolean")
     */
    private $isTplEnabled;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_risky", type="boolean")
     */
    private $isRisky;

    /**
     * @var int
     *
     * @ORM\Column(name="count_products", type="integer")
     */
    private $countProducts;

    /**
     * @var int
     *
     * @ORM\Column(name="rating", type="integer")
     */
    private $rating;

    /**
     * @var int
     *
     * @ORM\Column(name="delivery_tax", type="integer")
     */
    private $deliveryTax;

    /**
     * @var int
     *
     * @ORM\Column(name="lifting_tax", type="integer")
     */
    private $liftingTax;

    /**
     * @var Category[]
     *
     * @Assert\Type(type="array<Category>")
     */
    public $breadcrumbs;

    /**
     * @var CategorySeo
     *
     * @Assert\Type(type="CategorySeo")
     */
    public $seo;

    /**
     * @var Category[]
     *
     * @Assert\Type(type="array<Category>")
     */
    public $children;

    /**
     * @var bool
     *
     * @Assert\Type(type="boolean")
     */
    public $isLeaf;

    /**
     * @deprecated
     *
     * @var BaseProduct[]
     *
     * @Assert\Type(type="array<BaseProduct>")
     */
    public $products;

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
     * Set pid.
     *
     * @param int $pid
     *
     * @return Category
     */
    public function setPid($pid)
    {
        $this->pid = $pid;

        return $this;
    }

    /**
     * Get pid.
     *
     * @return int
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set aliasForId.
     *
     * @param int $aliasForId
     *
     * @return Category
     */
    public function setAliasForId($aliasForId)
    {
        $this->aliasForId = $aliasForId;

        return $this;
    }

    /**
     * Get aliasForId.
     *
     * @return int
     */
    public function getAliasForId()
    {
        return $this->aliasForId;
    }

    /**
     * Set useExname.
     *
     * @param string $useExname
     *
     * @return Category
     */
    public function setUseExname($useExname)
    {
        $this->useExname = $useExname;

        return $this;
    }

    /**
     * Get useExname.
     *
     * @return bool
     */
    public function getUseExname()
    {
        return $this->useExname;
    }

    /**
     * Set tpl.
     *
     * @param string $tpl
     *
     * @return Category
     */
    public function setTpl($tpl)
    {
        $this->tpl = $tpl;

        return $this;
    }

    /**
     * Get tpl.
     *
     * @return string
     */
    public function getTpl()
    {
        return $this->tpl;
    }

    /**
     * Set isTplEnabled.
     *
     * @param string $isTplEnabled
     *
     * @return Category
     */
    public function setIsTplEnabled($isTplEnabled)
    {
        $this->isTplEnabled = $isTplEnabled;

        return $this;
    }

    /**
     * Get isTplEnabled.
     *
     * @return bool
     */
    public function getIsTplEnabled()
    {
        return $this->isTplEnabled;
    }

    /**
     * Set isRisky.
     *
     * @param string $isRisky
     *
     * @return Category
     */
    public function setIsRisky($isRisky)
    {
        $this->isRisky = $isRisky;

        return $this;
    }

    /**
     * Get isRisky.
     *
     * @return bool
     */
    public function getIsRisky()
    {
        return $this->isRisky;
    }

    /**
     * Set countProducts.
     *
     * @param int $countProducts
     *
     * @return Category
     */
    public function setCountProducts($countProducts)
    {
        $this->countProducts = $countProducts;

        return $this;
    }

    /**
     * Get countProducts.
     *
     * @return int
     */
    public function getCountProducts()
    {
        return $this->countProducts;
    }

    /**
     * Set rating.
     *
     * @param int $rating
     *
     * @return Category
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating.
     *
     * @return int
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set deliveryTax.
     *
     * @param int $deliveryTax
     *
     * @return Category
     */
    public function setDeliveryTax($deliveryTax)
    {
        $this->deliveryTax = $deliveryTax;

        return $this;
    }

    /**
     * Get deliveryTax.
     *
     * @return int
     */
    public function getDeliveryTax()
    {
        return $this->deliveryTax;
    }

    /**
     * Set liftingTax.
     *
     * @param int $liftingTax
     *
     * @return Category
     */
    public function setLiftingTax($liftingTax)
    {
        $this->liftingTax = $liftingTax;

        return $this;
    }

    /**
     * Get liftingTax.
     *
     * @return int
     */
    public function getLiftingTax()
    {
        return $this->liftingTax;
    }
}
