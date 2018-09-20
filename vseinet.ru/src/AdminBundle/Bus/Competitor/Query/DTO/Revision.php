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
    public $link;

    /**
     * @Assert\Type(type="integer")
     */
    public $competitorPrice;

    /**
     * @Assert\Type(type="datetime")
     */
    public $priceTime;

    /**
     * @Assert\Type(type="datetime")
     */
    public $requestedAt;

    /**
     * @Enum("AppBundle\Enum\ProductToCompetitorStatus")
     */
    public $status;

    /**
     * @Enum("AppBunlde\Enum\ProductToCompetitorState")
     */
    public $state;

    /**
     * @Assert\Type(type="integer")
     */
    public $serverResponse;

    /**
     * @Assert\Type(type="boolean")
     */
    public $readOnly;
}
