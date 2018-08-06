<?php 

namespace ClaimsBundle\Bus\GoodsIssue\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class GetGoodsIssuesQuery extends Message
{
    /**
     * @VIA\Description("Goods issue id")
     */
    public $id;

    /**
     * @VIA\Description("Base product id")
     * @Assert\Type(type="integer")
     */
    public $pid;

    /**
     * @VIA\Description("Owner type")
     * @Assert\Type(type="integer")
     */
    public $ownerType;

    /**
     * @VIA\Description("In service")
     * @Assert\Type(type="integer")
     */
    public $inService;

    /**
     * @VIA\Description("Service Id")
     * @Assert\Type(type="integer")
     */
    public $serviceId;

    /**
     * @VIA\Description("Is getting")
     * @Assert\Type(type="integer")
     */
    public $isGetting;

    /**
     * @VIA\Description("Point Id")
     * @Assert\Type(type="array")
     */
    public $pointId;

    /**
     * @VIA\Description("Склад")
     * @Assert\Type(type="array")
     */
    public $warehouse;

    /**
     * @VIA\Description("Reclamation type")
     * @Assert\Type(type="array")
     */
    public $reclamationType;

    /**
     * @VIA\Description("Date since")
     * @Assert\Type(type="datetime")
     */
    public $dateSince;

    /**
     * @VIA\Description("Date till")
     * @Assert\Type(type="datetime")
     */
    public $dateTill;
}