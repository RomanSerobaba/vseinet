<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RepresentativePhoto
 *
 * @ORM\Table(name="representative_photo")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RepresentativePhotoRepository")
 */
class RepresentativePhoto
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
     * @ORM\Column(name="representative_id", type="integer")
     */
    private $representativeId;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string")
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string")
     */
    private $title;

    /**
     * @var int
     *
     * @ORM\Column(name="sort_order", type="integer")
     */
    private $sortOrder;


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
     * Set representativeId
     *
     * @param integer $representativeId
     *
     * @return RepresentativePhoto
     */
    public function setRepresentativeId($representativeId)
    {
        $this->representativeId = $representativeId;

        return $this;
    }

    /**
     * Get representativeId
     *
     * @return int
     */
    public function getRepresentativeId()
    {
        return $this->representativeId;
    }

    /**
     * Set url
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
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return RepresentativePhoto
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set sortOrder
     *
     * @param integer $sortOrder
     *
     * @return RepresentativePhoto
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    /**
     * Get sortOrder
     *
     * @return int
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }
}
