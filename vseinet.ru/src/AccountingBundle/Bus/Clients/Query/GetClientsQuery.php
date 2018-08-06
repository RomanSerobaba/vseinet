<?php 

namespace AccountingBundle\Bus\Clients\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class GetClientsQuery extends Message
{
    /**
     * @VIA\Description("Client type")
     * @Assert\Choice({"all", "cl", "wh", "org"}, strict=true)
     * @VIA\DefaultValue("all")
     */
    public $type;

    /**
     * @VIA\Description("Client name")
     * @Assert\Type(type="string")
     */
    public $lfs;

    /**
     * @VIA\Description("Client phone")
     * @Assert\Type(type="string")
     */
    public $phone;

    /**
     * @VIA\Description("Client email")
     * @Assert\Type(type="string")
     */
    public $email;

    /**
     * @VIA\Description("Sort by")
     * @Assert\Type(type="string")
     */
    public $sortBy;

    /**
     * @VIA\Description("Title")
     * @Assert\Type(type="string")
     */
    public $title;

    /**
     * @VIA\Description("Tin")
     * @Assert\Type(type="string")
     */
    public $tin;

    /**
     * @VIA\Description("City ID")
     * @Assert\Type(type="integer")
     */
    public $cityId;

}