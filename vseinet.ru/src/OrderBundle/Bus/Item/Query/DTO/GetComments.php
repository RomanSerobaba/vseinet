<?php 

namespace OrderBundle\Bus\Item\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class GetComments
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="integer")
     */
    public $orderItemId;

    /**
     * @Assert\Type(type="string")
     */
    public $text;

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
    public $type;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isImportant;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isCommon;

    /**
     * @Assert\Type(type="string")
     */
    public $commentator;
}