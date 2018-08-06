<?php 

namespace PricingBundle\Bus\Competitors\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class RevisionProducts
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="integer")
     */
    public $categoryId;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="datetime")
     */
    public $priceTime;

    /**
     * @Assert\Type(type="integer")
     */
    public $purchasePrice;

    /**
     * @Assert\Type(type="integer")
     */
    public $retailPrice;

    /**
     * @Assert\Type(type="integer")
     */
    public $createdBy;

    /**
     * @Assert\Type(type="string")
     */
    public $link;

    /**
     * @Assert\Type(type="array")
     */
    public $competitors;

    /**
     * RevisionProducts constructor.
     * @param $id
     * @param $categoryId
     * @param $name
     * @param $priceTime
     * @param $purchasePrice
     * @param $retailPrice
     * @param $createdBy
     * @param $link
     * @param array $competitors
     */
    public function __construct($id, $categoryId, $name, $priceTime, $purchasePrice, $retailPrice, $createdBy, $link, $competitors=[])
    {
        $this->id = $id;
        $this->categoryId = $categoryId;
        $this->name = $name;
        $this->priceTime = $priceTime;
        $this->purchasePrice = $purchasePrice;
        $this->retailPrice = $retailPrice;
        $this->createdBy = $createdBy;
        $this->link = $link;
        $this->competitors = $competitors;
    }
}