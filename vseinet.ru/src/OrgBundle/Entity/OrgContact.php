<?php

namespace OrgBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrgContact
 *
 * @ORM\Table(name="org_contact")
 * @ORM\Entity(repositoryClass="OrgBundle\Repository\OrgContactRepository")
 */
class OrgContact
{
    /**
     * @var int
     *
     * @ORM\Column(name="contact_id", type="integer")
     * @ORM\Id
     */
    private $contactId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="org_department_id", type="integer", nullable=true)
     */
    private $departmentId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="org_employee_user_id", type="integer", nullable=true)
     */
    private $userId;


    /**
     * Set contactId.
     *
     * @param int $contactId
     *
     * @return OrgContact
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

    /**
     * Set departmentId.
     *
     * @param int|null $departmentId
     *
     * @return OrgContact
     */
    public function setDepartmentId($departmentId = null)
    {
        $this->departmentId = $departmentId;

        return $this;
    }

    /**
     * Get departmentId.
     *
     * @return int|null
     */
    public function getDepartmentId()
    {
        return $this->departmentId;
    }

    /**
     * Set userId.
     *
     * @param int|null $userId
     *
     * @return OrgContact
     */
    public function setUserId($userId = null)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId.
     *
     * @return int|null
     */
    public function getUserId()
    {
        return $this->userId;
    }
}
