<?php

namespace OrgBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActivityObjectToIndex
 *
 * @ORM\Table(name="org_activity_object_to_index")
 * @ORM\Entity(repositoryClass="OrgBundle\Repository\ActivityObjectToIndexRepository")
 */
class ActivityObjectToIndex
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
     * @ORM\Column(name="org_activity_index_id", type="integer")
     * @ORM\Id
     */
    private $activityIndexId;


    /**
     * Set activityObjectId.
     *
     * @param int $activityObjectId
     *
     * @return ActivityObjectToIndex
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
     * Set activityIndexId.
     *
     * @param int $activityIndexId
     *
     * @return ActivityObjectToIndex
     */
    public function setActivityIndexId($activityIndexId)
    {
        $this->activityIndexId = $activityIndexId;

        return $this;
    }

    /**
     * Get activityIndexId.
     *
     * @return int
     */
    public function getActivityIndexId()
    {
        return $this->activityIndexId;
    }
}
