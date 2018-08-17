<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RepresentativePhone
 *
 * @ORM\Table(name="representative_phone")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RepresentativePhoneRepository")
 */
class RepresentativePhone
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
     * @ORM\Column(name="representative_id", type="integer")
     */
    private $representativeId;

    /**
     * @var int
     *
     * @ORM\Column(name="contact_id", type="integer")
     */
    private $contactId;


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
     * Set representativeId.
     *
     * @param int $representativeId
     *
     * @return RepresentativePhone
     */
    public function setRepresentativeId($representativeId)
    {
        $this->representativeId = $representativeId;

        return $this;
    }

    /**
     * Get representativeId.
     *
     * @return int
     */
    public function getRepresentativeId()
    {
        return $this->representativeId;
    }

    /**
     * Set contactId.
     *
     * @param int $contactId
     *
     * @return RepresentativePhone
     */
    public function setContactId($contactId)
    {
        $this->contactId = $contactId;

        return $this;
    }

    /**
     * Get contactId.
     *
     * @return int
     */
    public function getContactId()
    {
        return $this->contactId;
    }
}
