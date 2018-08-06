<?php

namespace ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Detail
 *
 * @ORM\Table(name="content_detail")
 * @ORM\Entity(repositoryClass="ContentBundle\Repository\DetailRepository")
 */
class Detail
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
     * @ORM\Column(name="content_detail_group_id", type="integer")
     */
    private $groupId;

    /**
     * @var int
     *
     * @ORM\Column(name="category_section_id", type="integer")
     */
    private $sectionId;

    /**
     * @var int
     *
     * @ORM\Column(name="pid", type="integer")
     */
    private $pid;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="content_detail_type_code", type="string")
     */
    private $typeCode;

    /**
     * @var int
     *
     * @ORM\Column(name="content_measure_unit_id", type="integer", nullable=true)
     */
    private $unitId;

    /**
     * @var array
     * 
     * @ORM\Column(name="substitutions", type="json_array", nullable=true)
     */
    private $substitutions;
    

    /**
     * @var int
     *
     * @ORM\Column(name="sort_order", type="integer")
     */
    private $sortOrder;


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
     * Set sectionId
     *
     * @param integer $sectionId
     *
     * @return Detail
     */
    public function setSectionId($sectionId)
    {
        $this->sectionId = $sectionId;

        return $this;
    }

    /**
     * Get sectionId
     *
     * @return int
     */
    public function getSectionId()
    {
        return $this->sectionId;
    }

    /**
     * Set groupId
     *
     * @param integer $groupId
     *
     * @return Detail
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;

        return $this;
    }

    /**
     * Get groupId
     *
     * @return int
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Set pid
     *
     * @param integer $pid
     *
     * @return Detail
     */
    public function setPid($pid)
    {
        $this->pid = $pid;

        return $this;
    }

    /**
     * Get pid
     *
     * @return int
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Detail
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set typeCode
     *
     * @param string $typeCode
     *
     * @return Detail
     */
    public function setTypeCode($typeCode)
    {
        $this->typeCode = $typeCode;

        return $this;
    }

    /**
     * Get typeCode
     *
     * @return string
     */
    public function getTypeCode()
    {
        return $this->typeCode;
    }

    /**
     * Set unitId
     *
     * @param integer $unitId
     *
     * @return Detail
     */
    public function setUnitId($unitId)
    {
        $this->unitId = $unitId;

        return $this;
    }

    /**
     * Get unitId
     *
     * @return int
     */
    public function getUnitId()
    {
        return $this->unitId;
    }

    /**
     * Set substitutions
     *
     * @param array $substitutions
     *
     * @return Detail
     */
    public function setSubstitutions($substitutions)
    {
        $this->substitutions = $substitutions;

        return $this;
    }

    /**
     * Get substitutions
     *
     * @return array
     */
    public function getSubstitutions()
    {
        return $this->substitutions;
    }

    /**
     * Set sortOrder
     *
     * @param integer $sortOrder
     *
     * @return Detail
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    /**
     * Get sortOrder
     *
     * @return int
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }
}

