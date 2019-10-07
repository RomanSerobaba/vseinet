<?php

namespace AdminBundle\Bus\Competitor\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints\Enum;

class Revision
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="string")
     */
    public $url;

    /**
     * @Assert\Type(type="integer")
     */
    public $price;

    /**
     * @Assert\Type(type="datetime")
     */
    public $completedAt;

    /**
     * @Assert\Type(type="datetime")
     */
    public $requestedAt;

    /**
     * @Enum("AppBunlde\Enum\ProductToCompetitorState")
     */
    public $state;

    /**
     * @Assert\Type(type="integer")
     */
    public $status;

    /**
     * @Assert\Type(type="boolean")
     */
    public $readOnly;
}
