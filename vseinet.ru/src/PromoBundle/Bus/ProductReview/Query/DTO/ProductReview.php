<?php 

namespace PromoBundle\Bus\ProductReview\Query\DTO;

use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class ProductReview
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $comment;

    /**
     * @Assert\Type(type="datetime")
     */
    public $createdAt;

    /**
     * @Assert\Type(type="integer")
     */
    public $createdBy;

    /**
     * @Assert\Type(type="string")
     */
    public $advantages;

    /**
     * @Assert\Type(type="string")
     */
    public $disadvantages;

    /**
     * @Assert\Type(type="integer")
     */
    public $estimate;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="integer")
     */
    public $baseProductId;

    /**
     * @Assert\Type(type="datetime")
     */
    public $approvedAt;

    /**
     * @Assert\Type(type="integer")
     */
    public $approvedBy;

    /**
     * @Assert\Type(type="datetime")
     */
    public $deletedAt;

    /**
     * @Assert\Type(type="integer")
     */
    public $deletedBy;

    /**
     * @Assert\Type(type="string")
     */
    public $contacts;

    /**
     * @Assert\Type(type="string")
     */
    public $answer;
}