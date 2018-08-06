<?php

namespace OrgBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmployeeRelative
 *
 * @ORM\Table(name="org_employee_relative")
 * @ORM\Entity(repositoryClass="OrgBundle\Repository\EmployeeRelativeRepository")
 */
class EmployeeRelative
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
     * @ORM\Column(name="org_employee_user_id", type="integer")
     */
    private $employeeUserId;

    /**
     * @var string
     *
     * @ORM\Column(name="relation", type="string", length=255)
     */
    private $relation;

    /**
     * @var int
     *
     * @ORM\Column(name="person_id", type="integer")
     */
    private $personId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="geo_address_id", type="integer", nullable=true)
     */
    private $geoAddressId;


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
     * Set employeeUserId.
     *
     * @param int $employeeUserId
     *
     * @return EmployeeRelative
     */
    public function setEmployeeUserId($employeeUserId)
    {
        $this->employeeUserId = $employeeUserId;

        return $this;
    }

    /**
     * Get employeeUserId.
     *
     * @return int
     */
    public function getEmployeeUserId()
    {
        return $this->employeeUserId;
    }

    /**
     * Set relation.
     *
     * @param string $relation
     *
     * @return EmployeeRelative
     */
    public function setRelation($relation)
    {
        $this->relation = $relation;

        return $this;
    }

    /**
     * Get relation.
     *
     * @return string
     */
    public function getRelation()
    {
        return $this->relation;
    }

    /**
     * Set personId.
     *
     * @param int $personId
     *
     * @return EmployeeRelative
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
     * Set geoAddressId.
     *
     * @param int|null $geoAddressId
     *
     * @return EmployeeRelative
     */
    public function setGeoAddressId($geoAddressId = null)
    {
        $this->geoAddressId = $geoAddressId;

        return $this;
    }

    /**
     * Get geoAddressId.
     *
     * @return int|null
     */
    public function getGeoAddressId()
    {
        return $this->geoAddressId;
    }
}
