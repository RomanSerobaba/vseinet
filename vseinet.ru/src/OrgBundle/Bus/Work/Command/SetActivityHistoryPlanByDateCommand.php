<?php

namespace OrgBundle\Bus\Work\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class SetActivityHistoryPlanByDateCommand extends Message
{
    /**
     * @Assert\Type(type="integer")
     * @Assert\NotBlank(message="Activity id can't be blank")
     */
    public $activityId;

    /**
     * @Assert\Type(type="string")
     * @Assert\Date()
     */
    public $date;

    /**
     * @Assert\Type(type="integer")
     */
    public $value;
}