<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ErrorReport.
 *
 * @ORM\Table(name="error_report")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ErrorReportRepository")
 */
class ErrorReport
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
     * @ORM\Column(name="sented_by", type="integer")
     */
    private $sentedBy;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string")
     */
    private $ip;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sented_at", type="datetime")
     */
    private $sentedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string")
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="node", type="string")
     */
    private $node;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text")
     */
    private $text;

    /**
     * @var int
     *
     * @ORM\Column(name="fixed_by", type="integer")
     */
    private $fixedBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fixed_at", type="datetime")
     */
    private $fixedAt;

    public function __construct()
    {
        $this->sentedAt = new \DateTime();
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
     * Set sentedBy.
     *
     * @param int $sentedBy
     *
     * @return ErrorReport
     */
    public function setSentedBy($sentedBy)
    {
        $this->sentedBy = $sentedBy;

        return $this;
    }

    /**
     * Get sentedBy.
     *
     * @return int
     */
    public function getSentedBy()
    {
        return $this->sentedBy;
    }

    /**
     * Set ip.
     *
     * @param string $ip
     *
     * @return ErrorReport
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip.
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set sentedAt.
     *
     * @param \DateTime $sentedAt
     *
     * @return ErrorReport
     */
    public function setSentedAt($sentedAt)
    {
        $this->sentedAt = $sentedAt;

        return $this;
    }

    /**
     * Get sentedAt.
     *
     * @return \DateTime
     */
    public function getSentedAt()
    {
        return $this->sentedAt;
    }

    /**
     * Set url.
     *
     * @param string $url
     *
     * @return ErrorReport
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
     * Set node.
     *
     * @param string $node
     *
     * @return ErrorReport
     */
    public function setNode($node)
    {
        $this->node = $node;

        return $this;
    }

    /**
     * Get node.
     *
     * @return string
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * Set text.
     *
     * @param string $text
     *
     * @return ErrorReport
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text.
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set fixedBy.
     *
     * @param int $fixedBy
     *
     * @return ErrorReport
     */
    public function setFixedBy($fixedBy)
    {
        $this->fixedBy = $fixedBy;

        return $this;
    }

    /**
     * Get fixedBy.
     *
     * @return int
     */
    public function getFixedBy()
    {
        return $this->fixedBy;
    }

    /**
     * Set fixedAt.
     *
     * @param \DateTime $fixedAt
     *
     * @return ErrorReport
     */
    public function setFixedAt($fixedAt)
    {
        $this->fixedAt = $fixedAt;

        return $this;
    }

    /**
     * Get fixedAt.
     *
     * @return \DateTime
     */
    public function getFixedAt()
    {
        return $this->fixedAt;
    }
}
