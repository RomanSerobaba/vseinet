<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ParserDetailValue.
 *
 * @ORM\Table(name="parser_detail_value")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ParserDetailValueRepository")
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
    private $parserDetailId;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string")
     */
    private $value;

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
     * Set parserDetailId.
     *
     * @param int $parserDetailId
     *
     * @return ParserDetailValue
     */
    public function setParserDetailId($parserDetailId)
    {
        $this->parserDetailId = $parserDetailId;

        return $this;
    }

    /**
     * Get parserDetailId.
     *
     * @return int
     */
    public function getParserDetailId()
    {
        return $this->parserDetailId;
    }

    /**
     * Set value.
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
     * Get value.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
