<?php 

namespace OrderBundle\Bus\Item\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class OrderComment
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $text;

    /**
     * @Assert\Type(type="datetime")
     */
    public $createdAt;

    /**
     * @Assert\Type(type="string")
     */
    public $type;

    /**
     * @Assert\Type(type="string")
     */
    public $creator;

    /**
     * Supplier constructor.
     *
     * @param $id
     * @param $code
     * @param $managerId
     * @param $count
     */
    public function __construct($id, $code, $managerId, $count)
    {
        $this->id = $id;
        $this->code = $code;
        $this->managerId = $managerId;
        $this->count = $count;
    }
}