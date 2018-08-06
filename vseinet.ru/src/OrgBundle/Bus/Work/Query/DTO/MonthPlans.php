<?php

namespace OrgBundle\Bus\Work\Query\DTO;

use OrgBundle\Entity\ActivityHistory;
use Symfony\Component\Validator\Constraints as Assert;

class MonthPlans
{
    /**
     * @var string
     *
     * @Assert\Type(type="string")
     */
    public $month;

    /**
     * @var \DateTime
     *
     * @Assert\Type(type="DateTime")
     */
    public $since;

    /**
     * @var \DateTime
     *
     * @Assert\Type(type="DateTime")
     */
    public $till;

    /**
     * @var ActivityHistory[]
     *
     * @Assert\All({
     *      @Assert\Type(type="OrgBundle\Entity\ActivityHistory")
     * })
     */
    public $plans;

    /**
     * MonthPlans constructor.
     * @param string $month
     * @param \DateTime|null $since
     * @param \DateTime|null $till
     * @param ActivityHistory[] $plans
     */
    public function __construct(
        string $month,
        \DateTime $since=null,
        \DateTime $till=null,
        array $plans=[]
    )
    {
        $this->month = $month;
        $this->since = $since;
        $this->till = $till;
        $this->plans = $plans;
    }
}