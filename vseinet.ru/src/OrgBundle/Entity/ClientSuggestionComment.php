<?php

namespace OrgBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ClientSuggestionComment
 *
 * @ORM\Table(name="client_suggestion_comment")
 * @ORM\Entity(repositoryClass="OrgBundle\Repository\ClientSuggestionCommentRepository")
 */
class ClientSuggestionComment
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
     * @ORM\Column(name="client_suggestion_id", type="integer")
     */
    private $clientSuggestionId;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="string", length=255)
     */
    private $text;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var int
     *
     * @ORM\Column(name="created_by", type="integer")
     */
    private $createdBy;


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
     * Set clientSuggestionId.
     *
     * @param int $clientSuggestionId
     *
     * @return ClientSuggestionComment
     */
    public function setClientSuggestionId($clientSuggestionId)
    {
        $this->clientSuggestionId = $clientSuggestionId;

        return $this;
    }

    /**
     * Get clientSuggestionId.
     *
     * @return int
     */
    public function getClientSuggestionId()
    {
        return $this->clientSuggestionId;
    }

    /**
     * Set text.
     *
     * @param string $text
     *
     * @return ClientSuggestionComment
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
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return ClientSuggestionComment
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set createdBy.
     *
     * @param int $createdBy
     *
     * @return ClientSuggestionComment
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
}
