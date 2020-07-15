<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ParserProduct.
 *
 * @ORM\Table(name="parser_product")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ParserProductRepository")
 */
class ParserProduct
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
     * @ORM\Column(name="parser_source_id", type="integer")
     */
    private $parserSourceId;

    /**
     * @var int
     *
     * @ORM\Column(name="partner_product_id", type="integer")
     */
    private $partnerProductId;

    /**
     * @var int
     *
     * @ORM\Column(name="target_partner_product_id", type="integer")
     */
    private $targetPartnerProductId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="base_product_id", type="integer", nullable=true)
     */
    private $baseProductId;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string")
     */
    private $url;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="parsed_at", type="datetime", nullable=true)
     */
    private $parsedAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="requested_at", type="datetime", nullable=true)
     */
    private $requestedAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="completed_at", type="datetime", nullable=true)
     */
    private $completedAt;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * Set defaults.
     */
    public function __construct()
    {
        $this->status = 0;
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
     * Set parserSourceId.
     *
     * @param int $parserSourceId
     *
     * @return ParserProduct
     */
    public function setParserSourceId($parserSourceId)
    {
        $this->parserSourceId = $parserSourceId;

        return $this;
    }

    /**
     * Get parserSourceId.
     *
     * @return int
     */
    public function getParserSourceId()
    {
        return $this->parserSourceId;
    }

    /**
     * Set partnerProductId.
     *
     * @param int $partnerProductId
     *
     * @return ParserProduct
     */
    public function setPartnerProductId($partnerProductId)
    {
        $this->partnerProductId = $partnerProductId;

        return $this;
    }

    /**
     * Get partnerProductId.
     *
     * @return int
     */
    public function getPartnerProductId()
    {
        return $this->partnerProductId;
    }

    /**
     * Set targetPartnerProductId.
     *
     * @param int $targetPartnerProductId
     *
     * @return ParserProduct
     */
    public function setTargetPartnerProductId($targetPartnerProductId)
    {
        $this->targetPartnerProductId = $targetPartnerProductId;

        return $this;
    }

    /**
     * Get targetPartnerProductId.
     *
     * @return int
     */
    public function getTargetPartnerProductId()
    {
        return $this->targetPartnerProductId;
    }

    /**
     * Set baseProductId.
     *
     * @param int|null $baseProductId
     *
     * @return ParserProduct
     */
    public function setBaseProductId($baseProductId = null)
    {
        $this->baseProductId = $baseProductId;

        return $this;
    }

    /**
     * Get baseProductId.
     *
     * @return int|null
     */
    public function getBaseProductId()
    {
        return $this->baseProductId;
    }

    /**
     * Set url.
     *
     * @param string $url
     *
     * @return ParserProduct
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set parsedAt.
     *
     * @param \DateTime|null $parsedAt
     *
     * @return ParserProduct
     */
    public function setParsedAt($parsedAt = null)
    {
        $this->parsedAt = $parsedAt;

        return $this;
    }

    /**
     * Get parsedAt.
     *
     * @return \DateTime|null
     */
    public function getParsedAt()
    {
        return $this->parsedAt;
    }

    /**
     * Set requestedAt.
     *
     * @param \DateTime|null $requestedAt
     *
     * @return ParserProduct
     */
    public function setRequestedAt($requestedAt = null)
    {
        $this->requestedAt = $requestedAt;

        return $this;
    }

    /**
     * Get requestedAt.
     *
     * @return \DateTime|null
     */
    public function getRequestedAt()
    {
        return $this->requestedAt;
    }

    /**
     * Set completedAt.
     *
     * @param \DateTime|null $completedAt
     *
     * @return ParserProduct
     */
    public function setCompletedAt($completedAt = null)
    {
        $this->completedAt = $completedAt;

        return $this;
    }

    /**
     * Get completedAt.
     *
     * @return \DateTime|null
     */
    public function getCompletedAt()
    {
        return $this->completedAt;
    }

    /**
     * Set status.
     *
     * @param int $status
     *
     * @return ParserProduct
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }
}
