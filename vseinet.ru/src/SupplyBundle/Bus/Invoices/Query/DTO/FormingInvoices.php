<?php 

namespace SupplyBundle\Bus\Invoices\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class FormingInvoices
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $name;
}