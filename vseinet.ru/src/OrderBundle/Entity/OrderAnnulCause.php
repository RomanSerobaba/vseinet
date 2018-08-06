<?php

namespace OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderAnnulCause
 *
 * @ORM\Table(name="order_annul_cause")
 * @ORM\Entity(repositoryClass="OrderBundle\Repository\OrderAnnulCauseRepository")
 */
class OrderAnnulCause
{
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=100, unique=true)
     * @ORM\Id
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;


    /**
     * Set code.
     *
     * @param string $code
     *
     * @return OrderAnnulCause
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
     * @return OrderAnnulCause
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
}
