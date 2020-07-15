<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ParserDetail.
 *
 * @ORM\Table(name="parser_detail")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ParserDetailRepository")
 */
class ParserDetail
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
     * @ORM\Column(name="parser_detail_group_id", type="integer")
     */
    private $parserDetailGroupId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_hidden", type="boolean")
     */
    private $isHidden;

    /**
     * Set defaults.
     */
    public function __construct()
    {
        $this->isHidden = false;
    }

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
     * Set parserDetailGroupId.
     *
     * @param int $parserDetailGroupId
     *
     * @return ParserDetail
     */
    public function setParserDetailGroupId($parserDetailGroupId)
    {
        $this->parserDetailGroupId = $parserDetailGroupId;

        return $this;
    }

    /**
     * Get parserDetailGroupId.
     *
     * @return int
     */
    public function getParserDetailGroupId()
    {
        return $this->parserDetailGroupId;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return ParserDetail
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
     * Set isHidden.
     *
     * @param bool $isHidden
     *
     * @return ParserDetail
     */
    public function setIsHidden($isHidden)
    {
        $this->isHidden = $isHidden;

        return $this;
    }

    /**
     * Get isHidden.
     *
     * @return bool
     */
    public function getIsHidden()
    {
        return $this->isHidden;
    }
}
