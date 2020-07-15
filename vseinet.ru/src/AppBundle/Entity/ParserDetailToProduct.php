<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ParserDetailToProduct.
 *
 * @ORM\Table(name="parser_detail_to_product")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ParserDetailToProductRepository")
 */
class ParserDetailToProduct
{
    /**
     * @var int
     *
     * @ORM\Column(name="parser_product_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $parserProductId;

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
     * @ORM\Column(name="parser_detail_value_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $parserDetailValueId;

    /**
     * Set parserProductId.
     *
     * @param int $parserProductId
     *
     * @return ParserDetailToProduct
     */
    public function setParserProductId($parserProductId)
    {
        $this->parserProductId = $parserProductId;

        return $this;
    }

    /**
     * Get parserProductId.
     *
     * @return int
     */
    public function getParserProductId()
    {
        return $this->parserProductId;
    }

    /**
     * Set parserDetailId.
     *
     * @param int $parserDetailId
     *
     * @return ParserDetailToProduct
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
     * Set parserDetailValueId.
     *
     * @param int $parserDetailValueId
     *
     * @return ParserDetailToProduct
     */
    public function setParserDetailValueId($parserDetailValueId)
    {
        $this->parserDetailValueId = $parserDetailValueId;

        return $this;
    }

    /**
     * Get parserDetailValueId.
     *
     * @return int
     */
    public function getParserDetailValueId()
    {
        return $this->parserDetailValueId;
    }
}
