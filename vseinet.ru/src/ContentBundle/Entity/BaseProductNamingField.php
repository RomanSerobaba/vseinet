<?php

namespace ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BaseProductNamingField
 *
 * @ORM\Table(name="base_product_naming_field")
 * @ORM\Entity(repositoryClass="ContentBundle\Repository\BaseProductNamingFieldRepository")
 */
class BaseProductNamingField
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string")
     */
    private $title;


    /**
     * Set name
     *
     * @param string $name
     *
     * @return BaseProductNamingField
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
     * Set title
     *
     * @param string $title
     *
     * @return BaseProductNamingField
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}

