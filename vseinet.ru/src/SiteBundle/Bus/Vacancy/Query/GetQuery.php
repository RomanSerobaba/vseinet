<?php 

namespace SiteBundle\Bus\Vacancy\Query;

use AppBundle\Bus\Message\Message;

class GetQuery extends Message
{
    /**
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     */
    public $id;
}