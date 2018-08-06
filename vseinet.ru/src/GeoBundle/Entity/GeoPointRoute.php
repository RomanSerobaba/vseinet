<?php

namespace GeoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GeoPointRoute
 *
 * @ORM\Table(name="geo_point_route")
 * @ORM\Entity(repositoryClass="GeoBundle\Repository\GeoPointRouteRepository")
 */
class GeoPointRoute
{
    /**
     * @var int
     *
     * @ORM\Column(name="starting_point_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $startingPointId;

    /**
     * @var int
     *
     * @ORM\Column(name="arrival_point_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $arrivalPointId;

    /**
     * @var string
     *
     * @ORM\Column(name="schedule", type="string")
     */
    private $schedule;


    /**
     * Set startingPointId.
     *
     * @param int $startingPointId
     *
     * @return GeoPointRoute
     */
    public function setStartingPointId($startingPointId)
    {
        $this->startingPointId = $startingPointId;

        return $this;
    }

    /**
     * Get startingPointId.
     *
     * @return int
     */
    public function getStartingPointId()
    {
        return $this->startingPointId;
    }

    /**
     * Set arrivalPointId.
     *
     * @param int $arrivalPointId
     *
     * @return GeoPointRoute
     */
    public function setArrivalPointId($arrivalPointId)
    {
        $this->arrivalPointId = $arrivalPointId;

        return $this;
    }

    /**
     * Get arrivalPointId.
     *
     * @return int
     */
    public function getArrivalPointId()
    {
        return $this->arrivalPointId;
    }

    /**
     * Set schedule.
     *
     * @param string $schedule
     *
     * @return GeoPointRoute
     */
    public function setSchedule($schedule)
    {
        $this->schedule = $schedule;

        return $this;
    }

    /**
     * Get schedule.
     *
     * @return string
     */
    public function getSchedule()
    {
        return $this->schedule;
    }
}
