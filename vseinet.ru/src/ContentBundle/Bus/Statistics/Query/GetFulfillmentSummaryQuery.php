<?php 

namespace ContentBundle\Bus\Statistics\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetFulfillmentSummaryQuery extends Message 
{
    /**
     * @Assert\Type(type="datetime")
     */
    public $fromDate;

    /**
     * @Assert\Type(type="datetime")
     */
    public $toDate;

    /**
     * @Assert\NotBlank(message="Значение managerId не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $managerId;
}