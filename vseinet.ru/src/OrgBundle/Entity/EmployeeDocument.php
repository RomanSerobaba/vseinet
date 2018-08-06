<?php

namespace OrgBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmployeeDocument
 *
 * @ORM\Table(name="org_employee_document")
 * @ORM\Entity(repositoryClass="OrgBundle\Repository\EmployeeDocumentRepository")
 */
class EmployeeDocument
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_necessary", type="boolean")
     */
    private $isNecessary;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_comment_allowed", type="boolean")
     */
    private $isCommentAllowed;

    /**
     * @var bool
     *
     * @ORM\Column(name="has_due_date", type="boolean")
     */
    private $hasDueDate;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;


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
     * Set name.
     *
     * @param string $name
     *
     * @return EmployeeDocument
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set isNecessary.
     *
     * @param bool $isNecessary
     *
     * @return EmployeeDocument
     */
    public function setIsNecessary($isNecessary)
    {
        $this->isNecessary = $isNecessary;

        return $this;
    }

    /**
     * Get isNecessary.
     *
     * @return bool
     */
    public function getIsNecessary()
    {
        return $this->isNecessary;
    }

    /**
     * Set isCommentAllowed.
     *
     * @param bool $isCommentAllowed
     *
     * @return EmployeeDocument
     */
    public function setIsCommentAllowed($isCommentAllowed)
    {
        $this->isCommentAllowed = $isCommentAllowed;

        return $this;
    }

    /**
     * Get isCommentAllowed.
     *
     * @return bool
     */
    public function getIsCommentAllowed()
    {
        return $this->isCommentAllowed;
    }

    /**
     * Set hasDueDate.
     *
     * @param bool $hasDueDate
     *
     * @return EmployeeDocument
     */
    public function setHasDueDate($hasDueDate)
    {
        $this->hasDueDate = $hasDueDate;

        return $this;
    }

    /**
     * Get hasDueDate.
     *
     * @return bool
     */
    public function getHasDueDate()
    {
        return $this->hasDueDate;
    }

    /**
     * Set isActive.
     *
     * @param bool $isActive
     *
     * @return EmployeeDocument
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive.
     *
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
}
