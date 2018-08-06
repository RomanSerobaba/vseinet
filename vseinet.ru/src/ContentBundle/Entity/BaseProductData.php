<?php

namespace ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BaseProductData
 *
 * @ORM\Table(name="base_product_data")
 * @ORM\Entity(repositoryClass="ContentBundle\Repository\BaseProductDataRepository")
 */
class BaseProductData
{
    /**
     * @var int
     *
     * @ORM\Column(name="base_product_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $baseProductId;

    /**
     * @var string
     *
     * @ORM\Column(name="model", type="string", nullable=true)
     */
    private $model;

    /**
     * @var string
     *
     * @ORM\Column(name="exname", type="string", nullable=true)
     */
    private $exname;

    /**
     * @var string
     *
     * @ORM\Column(name="manufacturer_link", type="string", nullable=true)
     */
    private $manufacturerLink;

    /**
     * @var string
     *
     * @ORM\Column(name="manual_link", type="string", nullable=true)
     */
    private $manualLink;

    /**
     * @var array
     *
     * @ORM\Column(name="details", type="json_array", nullable=true)
     */
    private $details;

    /**
     * @var string
     *
     * @ORM\Column(name="short_description", type="text", nullable=true)
     */
    private $shortDescription;

    /**
     * @var int
     *
     * @ORM\Column(name="base_product_measure_id", type="integer", nullable=true)
     */
    private $measureId;


    /**
     * Set baseProductId
     *
     * @param integer $baseProductId
     *
     * @return BaseProductData
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
     * Set model
     *
     * @param string $model
     *
     * @return BaseProductData
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get model
     *
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set exname
     *
     * @param string $exname
     *
     * @return BaseProductData
     */
    public function setExname($exname)
    {
        $this->exname = $exname;

        return $this;
    }

    /**
     * Get exname
     *
     * @return string
     */
    public function getExname()
    {
        return $this->exname;
    }

    /**
     * Set manufacturerLink
     *
     * @param string $manufacturerLink
     *
     * @return BaseProductData
     */
    public function setManufacturerLink($manufacturerLink)
    {
        $this->manufacturerLink = $manufacturerLink;

        return $this;
    }

    /**
     * Get manufacturerLink
     *
     * @return string
     */
    public function getManufacturerLink()
    {
        return $this->manufacturerLink;
    }

    /**
     * Set manualLink
     *
     * @param string $manualLink
     *
     * @return BaseProductData
     */
    public function setManualLink($manualLink)
    {
        $this->manualLink = $manualLink;

        return $this;
    }

    /**
     * Get manualLink
     *
     * @return string
     */
    public function getManualLink()
    {
        return $this->manualLink;
    }

    /**
     * Set details
     *
     * @param array $details
     *
     * @return BaseProductData
     */
    public function setDetails($details)
    {
        $this->details = $details;

        return $this;
    }

    /**
     * Get details
     *
     * @return array
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Set shortDescription
     *
     * @param string $shortDescription
     *
     * @return BaseProductData
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    /**
     * Get shortDescription
     *
     * @return string
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * Set measureId
     *
     * @param integer $measureId
     *
     * @return BaseProductData
     */
    public function setMeasureId($measureId)
    {
        $this->measureId = $measureId;

        return $this;
    }

    /**
     * Get measureId
     *
     * @return int
     */
    public function getMeasureId()
    {
        return $this->measureId;
    }
}

