<?php

namespace OrgBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CashDesk
 *
 * @ORM\Table(name="cash_desk")
 * @ORM\Entity(repositoryClass="OrgBundle\Repository\CashDeskRepository")
 */
class CashDesk
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="financial_resource_id_seq", initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_room_id", type="integer")
     */
    private $geoRoomId;

    /**
     * @var int
     *
     * @ORM\Column(name="collector_id", type="integer")
     */
    private $collectorId;

    /**
     * @var int
     *
     * @ORM\Column(name="org_department_id", type="integer")
     */
    private $departmentId;


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
     * Set title.
     *
     * @param string $title
     *
     * @return CashDesk
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set geoRoomId.
     *
     * @param int $geoRoomId
     *
     * @return CashDesk
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
     * Set collectorId.
     *
     * @param int $collectorId
     *
     * @return CashDesk
     */
    public function setCollectorId($collectorId)
    {
        $this->collectorId = $collectorId;

        return $this;
    }

    /**
     * Get collectorId.
     *
     * @return int
     */
    public function getCollectorId()
    {
        return $this->collectorId;
    }

    /**
     * Set departmentId.
     *
     * @param int $departmentId
     *
     * @return CashDesk
     */
    public function setDepartmentId($departmentId)
    {
        $this->departmentId = $departmentId;

        return $this;
    }

    /**
     * Get departmentId.
     *
     * @return int
     */
    public function getDepartmentId()
    {
        return $this->departmentId;
    }
}
