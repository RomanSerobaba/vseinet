<?php

namespace OrgBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmployeeToGeoRoom
 *
 * @ORM\Table(name="org_employee_to_geo_room")
 * @ORM\Entity(repositoryClass="OrgBundle\Repository\EmployeeToGeoRoomRepository")
 */
class EmployeeToGeoRoom
{
    /**
     * @var int
     *
     * @ORM\Column(name="org_employee_user_id", type="integer")
     * @ORM\Id
     */
    private $employeeId;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_room_id", type="integer")
     * @ORM\Id
     */
    private $geoRoomId;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_main", type="boolean", nullable=true)
     */
    private $isMain;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_accountable", type="boolean", nullable=true)
     */
    private $isAccountable;


    /**
     * Set employeeId.
     *
     * @param int $employeeId
     *
     * @return EmployeeToGeoRoom
     */
    public function setEmployeeId($employeeId)
    {
        $this->employeeId = $employeeId;

        return $this;
    }

    /**
     * Get employeeId.
     *
     * @return int
     */
    public function getEmployeeId()
    {
        return $this->employeeId;
    }

    /**
     * Set geoRoomId.
     *
     * @param int $geoRoomId
     *
     * @return EmployeeToGeoRoom
     */
    public function setGeoRoomId($geoRoomId)
    {
        $this->geoRoomId = $geoRoomId;

        return $this;
    }

    /**
     * Get geoRoomId.
     *
     * @return int
     */
    public function getGeoRoomId()
    {
        return $this->geoRoomId;
    }

    /**
     * Set isMain.
     *
     * @param bool|null $isMain
     *
     * @return EmployeeToGeoRoom
     */
    public function setIsMain($isMain = null)
    {
        $this->isMain = $isMain;

        return $this;
    }

    /**
     * Get isMain.
     *
     * @return bool|null
     */
    public function getIsMain()
    {
        return $this->isMain;
    }

    /**
     * Set isAccountable.
     *
     * @param bool|null $isAccountable
     *
     * @return EmployeeToGeoRoom
     */
    public function setIsAccountable($isAccountable = null)
    {
        $this->isAccountable = $isAccountable;

        return $this;
    }

    /**
     * Get isAccountable.
     *
     * @return bool|null
     */
    public function getIsAccountable()
    {
        return $this->isAccountable;
    }
}
