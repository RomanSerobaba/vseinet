<?php

namespace OrgBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActivityHistory
 *
 * @ORM\Table(name="org_activity_history")
 * @ORM\Entity(repositoryClass="OrgBundle\Repository\ActivityHistoryRepository")
 */
class ActivityHistory
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="org_activity_id", type="integer")
     */
    private $activityId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var int|null
     *
     * @ORM\Column(name="plan_amount", type="integer", nullable=true)
     */
    private $planAmount;

    /**
     * @var int|null
     *
     * @ORM\Column(name="fact_amount", type="integer", nullable=true)
     */
    private $factAmount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="calculated_at", type="datetime", nullable=true)
     */
    private $calculatedAt;

    /**
     * @var Activity
     *
     * @ORM\ManyToOne(targetEntity="OrgBundle\Entity\Activity")
     * @ORM\JoinColumn(name="org_activity_id", referencedColumnName="id")
     */
    private $activity;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set activityId.
     *
     * @param int $activityId
     *
     * @return ActivityHistory
     */
    public function setActivityId($activityId)
    {
        $this->activityId = $activityId;

        return $this;
    }

    /**
     * Get activityId.
     *
     * @return int
     */
    public function getActivityId()
    {
        return $this->activityId;
    }

    /**
     * Set date.
     *
     * @param \DateTime $date
     *
     * @return ActivityHistory
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set planAmount.
     *
     * @param int|null $planAmount
     *
     * @return ActivityHistory
     */
    public function setPlanAmount($planAmount = null)
    {
        $this->planAmount = $planAmount;

        return $this;
    }

    /**
     * Get planAmount.
     *
     * @return int|null
     */
    public function getPlanAmount()
    {
        return $this->planAmount;
    }

    /**
     * Set factAmount.
     *
     * @param int|null $factAmount
     *
     * @return ActivityHistory
     */
    public function setFactAmount($factAmount = null)
    {
        $this->factAmount = $factAmount;

        return $this;
    }

    /**
     * Get factAmount.
     *
     * @return int|null
     */
    public function getFactAmount()
    {
        return $this->factAmount;
    }

    /**
     * Set calculatedAt.
     *
     * @param \DateTime|null $calculatedAt
     *
     * @return ActivityHistory
     */
    public function setCalculatedAt($calculatedAt)
    {
        $this->calculatedAt = $calculatedAt;

        return $this;
    }

    /**
     * Get calculatedAt.
     *
     * @return \DateTime|null
     */
    public function getCalculatedAt()
    {
        return $this->calculatedAt;
    }

    /**
     * Set activity.
     *
     * @param Activity|null $activity
     *
     * @return ActivityHistory
     */
    public function setActivity($activity = null)
    {
        $this->setActivityId($activity->getId());
        $this->activity = $activity;

        return $this;
    }

    /**
     * Get activity.
     *
     * @return Activity|null
     */
    public function getActivity()
    {
        return $this->activity;
    }
}
