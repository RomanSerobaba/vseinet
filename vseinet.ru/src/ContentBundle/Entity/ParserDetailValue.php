<?php

namespace ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ParserDetailValue
 *
 * @ORM\Table(name="parser_detail_value")
 * @ORM\Entity(repositoryClass="ContentBundle\Repository\ParserDetailValueRepository")
 */
class ParserDetailValue
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
     * @ORM\Column(name="parser_detail_id", type="integer")
     */
    private $detailId;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string")
     */
    private $value;


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
     * Set detailId
     *
     * @param integer $detailId
     *
     * @return ParserDetailValue
     */
    public function setDetailId($detailId)
    {
        $this->detailId = $detailId;

        return $this;
    }

    /**
     * Get detailId
     *
     * @return int
     */
    public function getDetailId()
    {
        return $this->detailId;
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return ParserDetailValue
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
}

