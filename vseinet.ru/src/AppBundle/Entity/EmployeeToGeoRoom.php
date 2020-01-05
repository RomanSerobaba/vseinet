<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmployeeToGeoRoom.
 *
 * @ORM\Table(name="org_employee_to_geo_room")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EmployeeToGeoRoomRepository")
 */
class EmployeeToGeoRoom
{
    /**
     * @ORM\Column(name="org_employee_user_id", type="integer")
     * @ORM\Id
     *
     * @var int
     */
    private $employeeId;

    /**
     * @ORM\Column(name="geo_room_id", type="integer")
     * @ORM\Id
     *
     * @var int
     */
    private $geoRoomId;

    /**
     * @ORM\Column(name="is_main", type="boolean", nullable=true)
     *
     * @var bool|null
     */
    private $isMain;

    /**
     * @ORM\Column(name="is_accountable", type="boolean", nullable=true)
     *
     * @var bool|null
     */
    private $isAccountable;

    /**
     * Set employeeId.
     *
     * @param int $employeeId
     *
     * @return EmployeeToGeoRoom
     */
    public function setEmployeeId($employeeId): EmployeeToGeoRoom
    {
        $this->employeeId = $employeeId;

        return $this;
    }

    /**
     * Get employeeId.
     *
     * @return int
     */
    public function getEmployeeId(): int
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
    public function setGeoRoomId($geoRoomId): EmployeeToGeoRoom
    {
        $this->geoRoomId = $geoRoomId;

        return $this;
    }

    /**
     * Get geoRoomId.
     *
     * @return int
     */
    public function getGeoRoomId(): int
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
    public function setIsMain($isMain = null): EmployeeToGeoRoom
    {
        $this->isMain = $isMain;

        return $this;
    }

    /**
     * Get isMain.
     *
     * @return bool|null
     */
    public function getIsMain(): ?bool
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
    public function setIsAccountable($isAccountable = null): EmployeeToGeoRoom
    {
        $this->isAccountable = $isAccountable;

        return $this;
    }

    /**
     * Get isAccountable.
     *
     * @return bool|null
     */
    public function getIsAccountable(): ?bool
    {
        return $this->isAccountable;
    }
}
