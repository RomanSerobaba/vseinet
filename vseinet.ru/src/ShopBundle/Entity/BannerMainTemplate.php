<?php

namespace ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BannerMainTemplate
 *
 * @ORM\Table(name="banner_main_template")
 * @ORM\Entity(repositoryClass="ShopBundle\Repository\BannerMainTemplateRepository")
 */
class BannerMainTemplate
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="img_background_pc", type="string", length=255)
     */
    private $imgBackgroundPc;

    /**
     * @var string
     *
     * @ORM\Column(name="img_background_tablet", type="string", length=255)
     */
    private $imgBackgroundTablet;

    /**
     * @var string
     *
     * @ORM\Column(name="img_background_phone", type="string", length=255)
     */
    private $imgBackgroundPhone;

    /**
     * @var int
     *
     * @ORM\Column(name="pos_background_pc_x", type="integer")
     */
    private $posBackgroundPcX;

    /**
     * @var int
     *
     * @ORM\Column(name="pos_background_pc_y", type="integer")
     */
    private $posBackgroundPcY;

    /**
     * @var int
     *
     * @ORM\Column(name="pos_background_tablet_x", type="integer")
     */
    private $posBackgroundTabletX;

    /**
     * @var int
     *
     * @ORM\Column(name="pos_background_tablet_y", type="integer")
     */
    private $posBackgroundTabletY;

    /**
     * @var int
     *
     * @ORM\Column(name="pos_background_phone_x", type="integer")
     */
    private $posBackgroundPhoneX;

    /**
     * @var int
     *
     * @ORM\Column(name="pos_background_phone_y", type="integer")
     */
    private $posBackgroundPhoneY;


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
     * Set name.
     *
     * @param string $name
     *
     * @return BannerMainTemplate
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
     * Set imgBackgroundPc.
     *
     * @param string $imgBackgroundPc
     *
     * @return BannerMainTemplate
     */
    public function setImgBackgroundPc($imgBackgroundPc)
    {
        $this->imgBackgroundPc = $imgBackgroundPc;

        return $this;
    }

    /**
     * Get imgBackgroundPc.
     *
     * @return string
     */
    public function getImgBackgroundPc()
    {
        return $this->imgBackgroundPc;
    }

    /**
     * Set imgBackgroundTablet.
     *
     * @param string $imgBackgroundTablet
     *
     * @return BannerMainTemplate
     */
    public function setImgBackgroundTablet($imgBackgroundTablet)
    {
        $this->imgBackgroundTablet = $imgBackgroundTablet;

        return $this;
    }

    /**
     * Get imgBackgroundTablet.
     *
     * @return string
     */
    public function getImgBackgroundTablet()
    {
        return $this->imgBackgroundTablet;
    }

    /**
     * Set imgBackgroundPhone.
     *
     * @param string $imgBackgroundPhone
     *
     * @return BannerMainTemplate
     */
    public function setImgBackgroundPhone($imgBackgroundPhone)
    {
        $this->imgBackgroundPhone = $imgBackgroundPhone;

        return $this;
    }

    /**
     * Get imgBackgroundPhone.
     *
     * @return string
     */
    public function getImgBackgroundPhone()
    {
        return $this->imgBackgroundPhone;
    }

    /**
     * Set posBackgroundPcX.
     *
     * @param int $posBackgroundPcX
     *
     * @return BannerMainTemplate
     */
    public function setPosBackgroundPcX($posBackgroundPcX)
    {
        $this->posBackgroundPcX = $posBackgroundPcX;

        return $this;
    }

    /**
     * Get posBackgroundPcX.
     *
     * @return int
     */
    public function getPosBackgroundPcX()
    {
        return $this->posBackgroundPcX;
    }

    /**
     * Set posBackgroundPcY.
     *
     * @param int $posBackgroundPcY
     *
     * @return BannerMainTemplate
     */
    public function setPosBackgroundPcY($posBackgroundPcY)
    {
        $this->posBackgroundPcY = $posBackgroundPcY;

        return $this;
    }

    /**
     * Get posBackgroundPcY.
     *
     * @return int
     */
    public function getPosBackgroundPcY()
    {
        return $this->posBackgroundPcY;
    }

    /**
     * Set posBackgroundTabletX.
     *
     * @param int $posBackgroundTabletX
     *
     * @return BannerMainTemplate
     */
    public function setPosBackgroundTabletX($posBackgroundTabletX)
    {
        $this->posBackgroundTabletX = $posBackgroundTabletX;

        return $this;
    }

    /**
     * Get posBackgroundTabletX.
     *
     * @return int
     */
    public function getPosBackgroundTabletX()
    {
        return $this->posBackgroundTabletX;
    }

    /**
     * Set posBackgroundTabletY.
     *
     * @param int $posBackgroundTabletY
     *
     * @return BannerMainTemplate
     */
    public function setPosBackgroundTabletY($posBackgroundTabletY)
    {
        $this->posBackgroundTabletY = $posBackgroundTabletY;

        return $this;
    }

    /**
     * Get posBackgroundTabletY.
     *
     * @return int
     */
    public function getPosBackgroundTabletY()
    {
        return $this->posBackgroundTabletY;
    }

    /**
     * Set posBackgroundPhoneX.
     *
     * @param int $posBackgroundPhoneX
     *
     * @return BannerMainTemplate
     */
    public function setPosBackgroundPhoneX($posBackgroundPhoneX)
    {
        $this->posBackgroundPhoneX = $posBackgroundPhoneX;

        return $this;
    }

    /**
     * Get posBackgroundPhoneX.
     *
     * @return int
     */
    public function getPosBackgroundPhoneX()
    {
        return $this->posBackgroundPhoneX;
    }

    /**
     * Set posBackgroundPhoneY.
     *
     * @param int $posBackgroundPhoneY
     *
     * @return BannerMainTemplate
     */
    public function setPosBackgroundPhoneY($posBackgroundPhoneY)
    {
        $this->posBackgroundPhoneY = $posBackgroundPhoneY;

        return $this;
    }

    /**
     * Get posBackgroundPhoneY.
     *
     * @return int
     */
    public function getPosBackgroundPhoneY()
    {
        return $this->posBackgroundPhoneY;
    }
}
