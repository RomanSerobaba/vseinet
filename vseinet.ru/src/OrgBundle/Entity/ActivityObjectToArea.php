<?php

namespace OrgBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActivityObjectToArea
 *
 * @ORM\Table(name="org_activity_object_to_area")
 * @ORM\Entity(repositoryClass="OrgBundle\Repository\ActivityObjectToAreaRepository")
 */
class ActivityObjectToArea
{
    /**
     * @var int
     *
     * @ORM\Column(name="org_activity_object_id", type="integer")
     * @ORM\Id
     */
    private $activityObjectId;

    /**
     * @var int
     *
     * @ORM\Column(name="org_activity_area_id", type="integer")
     * @ORM\Id
     */
    private $activityAreaId;


    /**
     * Set activityObjectId.
     *
     * @param int $activityObjectId
     *
     * @return ActivityObjectToArea
     */
    public function setActivityObjectId($activityObjectId)
    {
        $this->activityObjectId = $activityObjectId;

        return $this;
    }

    /**
     * Get activityObjectId.
     *
     * @return int
     */
    public function getActivityObjectId()
    {
        return $this->activityObjectId;
    }

    /**
     * Set activityAreaId.
     *
     * @param int $activityAreaId
     *
     * @return ActivityObjectToArea
     */
    public function setActivityAreaId($activityAreaId)
    {
        $this->activityAreaId = $activityAreaId;

        return $this;
    }

    /**
     * Get activityAreaId.
     *
     * @return int
     */
    public function getActivityAreaId()
    {
        return $this->activityAreaId;
    }
}
