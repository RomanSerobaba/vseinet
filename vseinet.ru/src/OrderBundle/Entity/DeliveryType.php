<?php

namespace OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DeliveryType
 *
 * @ORM\Table(name="delivery_type")
 * @ORM\Entity(repositoryClass="OrderBundle\Repository\DeliveryTypeRepository")
 */
class DeliveryType
{
    const CODE_EX_WORKS = 'ex_works';
    const CODE_COURIER = 'courier';
    const CODE_POST = 'post';
    const CODE_TRANSPORT_COMPANY = 'transport_company';

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
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_remote", type="boolean", nullable=true)
     */
    private $isRemote;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;


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
     * Set code
     *
     * @param string $code
     *
     * @return DeliveryType
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return DeliveryType
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
     * Set isRemote
     *
     * @param boolean $isRemote
     *
     * @return DeliveryType
     */
    public function setIsRemote($isRemote)
    {
        $this->isRemote = $isRemote;

        return $this;
    }

    /**
     * Get isRemote
     *
     * @return bool
     */
    public function getIsRemote()
    {
        return $this->isRemote;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return DeliveryType
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
}

