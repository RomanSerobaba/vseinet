<?php

namespace ClaimsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GoodsIssueComment
 *
 * @ORM\Table(name="goods_issue_comment")
 * @ORM\Entity(repositoryClass="ClaimsBundle\Repository\GoodsIssueCommentRepository")
 */
class GoodsIssueComment
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
     * @ORM\Column(name="goods_issue_id", type="integer")
     */
    private $goodsIssueId;

    /**
     * @var int
     *
     * @ORM\Column(name="created_by", type="integer")
     */
    private $createdBy;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="string", length=255)
     */
    private $text;


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
     * Set goodsIssueId.
     *
     * @param int $goodsIssueId
     *
     * @return GoodsIssueComment
     */
    public function setGoodsIssueId($goodsIssueId)
    {
        $this->goodsIssueId = $goodsIssueId;

        return $this;
    }

    /**
     * Get goodsIssueId.
     *
     * @return int
     */
    public function getGoodsIssueId()
    {
        return $this->goodsIssueId;
    }

    /**
     * Set createdBy.
     *
     * @param int $createdBy
     *
     * @return GoodsIssueComment
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy.
     *
     * @return int
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime|null $createdAt
     *
     * @return GoodsIssueComment
     */
    public function setCreatedAt($createdAt = null)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime|null
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set text.
     *
     * @param string $text
     *
     * @return GoodsIssueComment
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
}
