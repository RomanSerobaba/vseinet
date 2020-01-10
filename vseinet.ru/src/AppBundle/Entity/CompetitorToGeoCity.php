<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CompetitorToGeoCity
 *
 * @ORM\Table(name="competitor_to_geo_city")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CompetitorToGeoCityRepository")
 */
class CompetitorToGeoCity
{
    /**
     * @var int
     *
     * @ORM\Column(name="competitor_id", type="integer")
     * @ORM\Id
     */
    private $competitorId;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_city_id", type="integer")
     * @ORM\Id
     */
    private $geoCityId;


    /**
     * Set competitorId
     *
     * @param integer $competitorId
     *
     * @return CompetitorToGeoCity
     */
    public function setCompetitorId($competitorId)
    {
        $this->competitorId = $competitorId;

        return $this;
    }

    /**
     * Get competitorId
     *
     * @return int
     */
    public function getCompetitorId()
    {
        return $this->competitorId;
    }

    /**
     * Set geoCityId
     *
     * @param integer $geoCityId
     *
     * @return CompetitorToGeoCity
     */
    public function setGeoCityId($geoCityId)
    {
        $this->geoCityId = $geoCityId;

        return $this;
    }

    /**
     * Get geoCityId
     *
     * @return int
     */
    public function getGeoCityId()
    {
        return $this->geoCityId;
    }
}

