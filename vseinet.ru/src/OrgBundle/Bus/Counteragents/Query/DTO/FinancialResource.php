<?php

namespace OrgBundle\Bus\Counteragents\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class FinancialResource
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $title;

    /**
     * @Assert\Type(type="string")
     */
    public $type;

    /**
     * @Assert\Type(type="string")
     */
    public $data;

    /**
     * FinancialResource constructor.
     * @param $id
     * @param $title
     * @param $type
     * @param $data
     */
    public function __construct($id, $title, $type=null, $data=null)
    {
        $this->id = $id;
        $this->title = $title;
        $this->type = $type;
        $this->data = $data;
    }
}