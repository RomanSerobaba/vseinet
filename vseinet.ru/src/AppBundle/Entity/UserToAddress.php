<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserToAddress
 *
 * @ORM\Table(name="user_to_address")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserToAddressRepository")
 */
class UserToAddress
{
    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer")
     * @ORM\Id
     */
    private $userId;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_address_id", type="integer")
     * @ORM\Id
     */
    private $geoAddressId;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_default", type="boolean")
     */
    private $isDefault;


    /**
     * Set userId.
     *
     * @param int $userId
     *
     * @return UserToAddress
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId.
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set geoAddressId.
     *
     * @param int $geoAddressId
     *
     * @return UserToAddress
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
     * Set isDefault.
     *
     * @param bool $isDefault
     *
     * @return UserToAddress
     */
    public function setIsDefault($isDefault)
    {
        $this->isDefault = $isDefault;

        return $this;
    }

    /**
     * Get isDefault.
     *
     * @return bool
     */
    public function getIsDefault()
    {
        return $this->isDefault;
    }
}
