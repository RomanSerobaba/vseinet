<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Role.
 *
 * @ORM\Table(name="acl_role")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RoleRepository")
 */
class Role
{
    public const CODE_CLIENT = 'CLIENT';
    public const CODE_MANAGER = 'MANAGER';
    public const CODE_STOREKEEPER = 'STOREKEEPER';
    public const CODE_CONTENTER = 'CONTENTER';
    public const CODE_PURCHASER = 'PURCHASER';
    public const CODE_LOGISTICIAN = 'LOGISTICIAN';
    public const CODE_CASHIER = 'CASHIER';
    public const CODE_BOOKKEEPER = 'BOOKKEEPER';
    public const CODE_ADMIN = 'ADMIN';
    public const CODE_RIGGER = 'RIGGER';
    public const CODE_SERVICER = 'SERVICER';
    public const CODE_FREELANCER = 'FREELANCER';
    public const CODE_PERSONNELIER = 'PERSONNELIER';
    public const CODE_PROGRAMMER = 'PROGRAMMER';
    public const CODE_FRANCHISER = 'FRANCHISER';
    public const CODE_WHOLESALER = 'WHOLESALER';
    public const CODE_DRIVER = 'DRIVER';
    public const CODE_PARTNER = 'PARTNER';

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
     * @ORM\Column(name="code", type="string")
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="sort_order", type="integer")
     */
    private $sortOrder;

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
     * Set code.
     *
     * @param string $code
     *
     * @return Role
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code.
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Role
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
     * Set sortOrder.
     *
     * @param string $sortOrder
     *
     * @return Role
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    /**
     * Get sortOrder.
     *
     * @return string
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }
}
