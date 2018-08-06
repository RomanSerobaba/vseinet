<?php

namespace ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DetailValueAlias
 *
 * @ORM\Table(name="content_detail_value_alias")
 * @ORM\Entity(repositoryClass="ContentBundle\Repository\DetailValueAliasRepository")
 */
class DetailValueAlias
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
     * @ORM\Column(name="value", type="string")
     */
    private $value;

    /**
     * @var int
     *
     * @ORM\Column(name="content_detail_value_id", type="integer")
     */
    private $valueId;


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
     * Set value
     *
     * @param string $value
     *
     * @return DetailValueAlias
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set valueId
     *
     * @param integer $valueId
     *
     * @return DetailValueAlias
     */
    public function setValueId($valueId)
    {
        $this->valueId = $valueId;

        return $this;
    }

    /**
     * Get valueId
     *
     * @return int
     */
    public function getValueId()
    {
        return $this->valueId;
    }
}

