<?php

namespace AppBundle\Bus\Catalog\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Category
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="string")
     */
    public $chpuName;

    /**
     * @Assert\Type(type="integer")
     */
    public $aliasForId;

    /**
     * @Assert\Type(type="integer")
     */
    public $countProducts;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isLeaf;

    /**
     * @Assert\Type(type="string")
     */
    public $title;

    /**
     * @Assert\Type(type="string")
     */
    public $description;

    /**
     * @Assert\Type(type="string")
     */
    public $pageTitle;

    /**
     * @Assert\Type(type="string")
     */
    public $pageDescription;

    /**
     * @Assert\type(type="boolean")
     */
    public $isTplEnabled;

    /**
     * @Assert\Type(type="array<AppBundle\Bus\Catalog\Query\DTO\Breadcrumb>")
     */
    public $breadcrumbs = [];

    /**
     * @Assert\Type(type="AppBundle\Bus\Catalog\Query\DTO\Image")
     */
    public $image;


    public function __construct(
        $id,
        $name,
        $aliasForId,
        $countProducts,
        $isLeaf,
        $title,
        $description,
        $pageTitle,
        $pageDescription,
        $isTplEnabled,
        $chpuName
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->aliasForId = $aliasForId;
        $this->countProducts = $countProducts;
        $this->isLeaf = $isLeaf;
        $this->title = $title;
        $this->description = $description;
        $this->pageTitle = $pageTitle;
        $this->pageDescription = $pageDescription;
        $this->isTplEnabled = $isTplEnabled;
        $this->chpuName = $chpuName;
    }
}
