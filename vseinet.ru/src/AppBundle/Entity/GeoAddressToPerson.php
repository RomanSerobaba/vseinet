<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GeoAddressToPerson
 *
 * @ORM\Table(name="geo_address_to_person")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GeoAddressToPersonRepository")
 */
class GeoAddressToPerson
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="geo_address_id", type="integer")
     */
    private $geoAddressId;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="person_id", type="integer")
     */
    private $personId;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_main", type="boolean")
     */
    private $isMain;
    

    /**
     * Set geoAddressId.
     *
     * @param int $geoAddressId
     *
     * @return GeoAddressToPerson
     */
    public function setGeoAddressId($geoAddressId)
    {
        $this->geoAddressId = $geoAddressId;

        return $this;
    }

    /**
     * Get geoAddressId.
     *
     * @return int
     */
    public function getGeoAddressId()
    {
        return $this->geoAddressId;
    }

    /**
     * Set personId.
     *
     * @param int $personId
     *
     * @return GeoAddressToPerson
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;

        return $this;
    }

    /**
     * Get personId.
     *
     * @return int
     */
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     * Set isMain.
     *
     * @param bool $isMain
     *
     * @return GeoAddressToPerson
     */
    public function setIsMain($isMain)
    {
        $this->isMain = $isMain;

        return $this;
    }

    /**
     * Get isMain.
     *
     * @return bool
     */
    public function getIsMain()
    {
        return $this->isMain;
    }
}
