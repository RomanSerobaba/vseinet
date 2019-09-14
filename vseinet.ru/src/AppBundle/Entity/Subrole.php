<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Subrole.
 *
 * @ORM\Table(name="acl_subrole")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SubroleRepository")
 */
class Subrole
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
     * @ORM\Column(name="acl_role_id", type="integer")
     */
    private $roleId;

    /**
     * @var int
     *
     * @ORM\Column(name="grade", type="integer")
     */
    private $grade;

    /**
     * @deprecated
     * @ORM\ManyToOne(targetEntity="Role", inversedBy="subroles")
     * @ORM\JoinColumn(name="acl_role_id", referencedColumnName="id")
     */
    //public $role;

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
     * Set roleId.
     *
     * @param int $roleId
     *
     * @return Subrole
     */
    public function setRoleId($roleId)
    {
        $this->roleId = $roleId;

        return $this;
    }

    /**
     * Get roleId.
     *
     * @return int
     */
    public function getRoleId()
    {
        return $this->roleId;
    }

    /**
     * Set grade.
     *
     * @param int $grade
     *
     * @return Subrole
     */
    public function setGrade($grade)
    {
        $this->grade = $grade;

        return $this;
    }

    /**
     * Get grade.
     *
     * @return int
     */
    public function getGrade()
    {
        return $this->grade;
    }
}
