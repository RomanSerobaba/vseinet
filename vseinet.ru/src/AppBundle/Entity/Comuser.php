<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Comuser
 *
 * @ORM\Table(name="comuser")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ComuserRepository")
 */
class Comuser
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
     * @var string
     *
     * @ORM\Column(name="fullname", type="string")
     */
    private $fullname;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string")
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="additional_phone", type="string")
     */
    private $additionalPhone;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string")
     */
    private $email;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_address_id", type="integer")
     */
    private $geoAddressId;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fullname
     *
     * @param string $fullname
     *
     * @return Comuser
     */
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;

        return $this;
    }

    /**
     * Get fullname
     *
     * @return string
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Comuser
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set additionalPhone
     *
     * @param string $additionalPhone
     *
     * @return Comuser
     */
    public function setAdditionalPhone($additionalPhone)
    {
        $this->additionalPhone = $additionalPhone;

        return $this;
    }

    /**
     * Get additionalPhone
     *
     * @return string
     */
    public function getAdditionalPhone()
    {
        return $this->additionalPhone;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Comuser
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set geoAddressId
     *
     * @param integer $geoAddressId
     *
     * @return Comuser
     */
    public function setGeoAddressId($geoAddressId)
    {
        $this->geoAddressId = $geoAddressId;

        return $this;
    }

    /**
     * Get geoAddressId
     *
     * @return int
     */
    public function getGeoAddressId()
    {
        return $this->geoAddressId;
    }
}

