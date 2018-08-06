<?php

namespace OrgBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RepresentativePhoto
 *
 * @ORM\Table(name="representative_photo")
 * @ORM\Entity(repositoryClass="OrgBundle\Repository\RepresentativePhotoRepository")
 */
class RepresentativePhoto
{
    const WEB_DIR_URL = '/u/contacts';

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
     * @ORM\Column(name="represenative_id", type="integer")
     */
    private $represenativeId;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var string|null
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var int|null
     *
     * @ORM\Column(name="sort_order", type="integer", nullable=true)
     */
    private $sortOrder;


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
     * Set represenativeId.
     *
     * @param int $represenativeId
     *
     * @return RepresentativePhoto
     */
    public function setRepresenativeId($represenativeId)
    {
        $this->represenativeId = $represenativeId;

        return $this;
    }

    /**
     * Get represenativeId.
     *
     * @return int
     */
    public function getRepresenativeId()
    {
        return $this->represenativeId;
    }

    /**
     * Set url.
     *
     * @param string $url
     *
     * @return RepresentativePhoto
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
     * Set title.
     *
     * @param string|null $title
     *
     * @return RepresentativePhoto
     */
    public function setTitle($title = null)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set sortOrder.
     *
     * @param int|null $sortOrder
     *
     * @return RepresentativePhoto
     */
    public function setSortOrder($sortOrder = null)
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    /**
     * Get sortOrder.
     *
     * @return int|null
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }
}
