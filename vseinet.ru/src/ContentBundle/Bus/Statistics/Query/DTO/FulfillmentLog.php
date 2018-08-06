<?php 

namespace ContentBundle\Bus\Statistics\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class FulfillmentLog
{    
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="datetime")
     */
    public $createdAt;

    /**
     * @Assert\Type(type="string")
     */
    public $managerName;

    /**
     * @Assert\Type(type="integer")
     */
    public $productId;

    /**
     * @Assert\Type(type="string")
     */
    public $productName;

    /**
     * @Assert\Choice({
     *     "in-process", 
     *     "skipped", 
     *     "done"
     * }, strict=true)
     */
    public $status;

    /**
     * @Assert\Type(type="float")
     */
    public $cost; 


    public function __construct($id, $createdAt, $managerName, $productId, $productName, $status, $cost)
    {
        $this->id = $id;
        $this->createdAt = $createdAt;
        $this->managerName = $managerName;
        $this->productId = $productId;
        $this->productName = $productName;
        $this->status = $status;
        $this->cost = $cost;
    }
}