<?php

namespace ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ParserDetailToContentDetail
 *
 * @ORM\Table(name="parser_detail_to_content_detail")
 * @ORM\Entity(repositoryClass="ContentBundle\Repository\ParserDetailToContentDetailRepository")
 */
class ParserDetailToContentDetail
{
    /**
     * @var int
     *
     * @ORM\Column(name="parser_detail_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $parserDetailId;

    /**
     * @var int
     *
     * @ORM\Column(name="content_detail_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $contentDetailId;


    /**
     * Set parserDetailId
     *
     * @param integer $parserDetailId
     *
     * @return ParserDetailToContentDetail
     */
    public function setParserDetailId($parserDetailId)
    {
        $this->parserDetailId = $parserDetailId;

        return $this;
    }

    /**
     * Get parserDetailId
     *
     * @return int
     */
    public function getParserDetailId()
    {
        return $this->parserDetailId;
    }

    /**
     * Set contentDetailId
     *
     * @param integer $contentDetailId
     *
     * @return ParserDetailToContentDetail
     */
    public function setContentDetailId($contentDetailId)
    {
        $this->contentDetailId = $contentDetailId;

        return $this;
    }

    /**
     * Get contentDetailId
     *
     * @return int
     */
    public function getContentDetailId()
    {
        return $this->contentDetailId;
    }
}

