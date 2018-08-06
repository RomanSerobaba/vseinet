<?php 

namespace SupplyBundle\Bus\Data\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GetFor1CQuery extends Message
{
    /**
     * @VIA\Description("ourTin")
     */
    public $ourTin;

    /**
     * @VIA\Description("tin")
     */
    public $tin;

    /**
     * @VIA\Description("supplierId")
     */
    public $supplierId;

    /**
     * @VIA\Description("ourWaybillNumber")
     */
    public $ourWaybillNumber;

    /**
     * @VIA\Description("waybillNumber")
     */
    public $waybillNumber;

    /**
     * @VIA\Description("Date")
     */
    public $date;
}