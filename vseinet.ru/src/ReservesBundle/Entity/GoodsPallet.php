<?php

namespace ReservesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * GoodsPallet
 *
 * @ORM\Table(name="goods_pallet")
 * @ORM\Entity(repositoryClass="ReservesBundle\Repository\GoodsPalletRepository")
 */
class GoodsPallet
{
    // <editor-fold defaultstate="collapsed" desc="Поля">    
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string")
     */
    private $title;
    
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
     * @var int
     *
     * @ORM\Column(name="geo_point_id", type="integer")
     */
    private $geoPointId;
    
    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string")
     */
    private $status;
    
    
    // <editor-fold defaultstate="collapsed" desc="Методы">    
    
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
     * Get created date and time
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set created date and time
     *
     * @param \DateTime $createdAt
     *
     * @return GoodsPallet
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->name;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return GoodsPallet
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }
    
    /**
     * Get geoPointId
     *
     * @return int
     */
    public function getGeoPointId()
    {
        return $this->geoPointId;
    }

    /**
     * Set geoPointId
     *
     * @param int $status
     *
     * @return GoodsPallet
     */
    public function setGeoPointId($geoPointId)
    {
        $this->geoPointId = $geoPointId;

        return $this;
    }
    
    /**
     * Get createdBy
     *
     * @return int
     */
    public function getCreatedBy()
    {
        return $this->geoPointId;
    }

    /**
     * Set createdBy
     *
     * @param int $createdBy
     *
     * @return GoodsPallet
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }
    
    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return GoodsPallet
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }
    
    // </editor-fold>
}

