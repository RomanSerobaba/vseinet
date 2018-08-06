<?php

namespace SupplyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Supplier
 *
 * @ORM\Table(name="supplier")
 * @ORM\Entity(repositoryClass="SupplyBundle\Repository\SupplierRepository")
 */
class Supplier
{
    const ME = 6;
    const TW = 86;
    const OR = 112;
    const ORH = 124;

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
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="approved_at", type="datetime", nullable=true)
     */
    private $approvedAt;

    /**
     * @var int|null
     *
     * @ORM\Column(name="approved_by", type="integer", nullable=true)
     */
    private $approvedBy;

    /**
     * @var string|null
     *
     * @ORM\Column(name="site_url", type="string", length=255, nullable=true)
     */
    private $siteUrl;

    /**
     * @var string|null
     *
     * @ORM\Column(name="auth_url", type="string", length=255, nullable=true)
     */
    private $authUrl;

    /**
     * @var string|null
     *
     * @ORM\Column(name="auth_login", type="string", length=255, nullable=true)
     */
    private $authLogin;

    /**
     * @var string|null
     *
     * @ORM\Column(name="auth_password", type="string", length=255, nullable=true)
     */
    private $authPassword;

    /**
     * @var string|null
     *
     * @ORM\Column(name="auth_comment", type="string", length=255, nullable=true)
     */
    private $authComment;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="contract_till", type="datetime", nullable=true)
     */
    private $contractTill;

    /**
     * @var int|null
     *
     * @ORM\Column(name="contract_updated_by", type="integer", nullable=true)
     */
    private $contractUpdatedBy;

    /**
     * @var int|null
     *
     * @ORM\Column(name="geo_point_id", type="integer", nullable=true)
     */
    private $geoPointId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="order_threshold_schedule", type="string", length=255, nullable=true)
     */
    private $orderThresholdSchedule;

    /**
     * @var string|null
     *
     * @ORM\Column(name="order_delivery_schedule", type="string", length=255, nullable=true)
     */
    private $orderDeliverySchedule;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="order_threshold_time", type="datetime", nullable=true)
     */
    private $orderThresholdTime;

    /**
     * @var bool
     *
     * @ORM\Column(name="has_free_delivery", type="boolean")
     */
    private $hasFreeDelivery;

    /**
     * @var int|null
     *
     * @ORM\Column(name="manager_id", type="integer", nullable=true)
     */
    private $managerId;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;


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
     * @return Supplier
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
     * Set code.
     *
     * @param string $code
     *
     * @return Supplier
     */
    public function setCode($code)
    {
        if (!preg_match("/^[A-Z]+$/", $code)) throw new BadRequestHttpException('Неверный код поставщика');

        $this->code = $code;

        return $this;
    }

    /**
     * Get code.
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set description.
     *
     * @param string|null $description
     *
     * @return Supplier
     */
    public function setDescription($description = null)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set approvedAt.
     *
     * @param \DateTime|null $approvedAt
     *
     * @return Supplier
     */
    public function setApprovedAt($approvedAt = null)
    {
        $this->approvedAt = $approvedAt;

        return $this;
    }

    /**
     * Get approvedAt.
     *
     * @return \DateTime|null
     */
    public function getApprovedAt()
    {
        return $this->approvedAt;
    }

    /**
     * Set approvedBy.
     *
     * @param int|null $approvedBy
     *
     * @return Supplier
     */
    public function setApprovedBy($approvedBy = null)
    {
        $this->approvedBy = $approvedBy;

        return $this;
    }

    /**
     * Get approvedBy.
     *
     * @return int|null
     */
    public function getApprovedBy()
    {
        return $this->approvedBy;
    }

    /**
     * Set siteUrl.
     *
     * @param string|null $siteUrl
     *
     * @return Supplier
     */
    public function setSiteUrl($siteUrl = null)
    {
        $this->siteUrl = $siteUrl;

        return $this;
    }

    /**
     * Get siteUrl.
     *
     * @return string|null
     */
    public function getSiteUrl()
    {
        return $this->siteUrl;
    }

    /**
     * Set authUrl.
     *
     * @param string|null $authUrl
     *
     * @return Supplier
     */
    public function setAuthUrl($authUrl = null)
    {
        $this->authUrl = $authUrl;

        return $this;
    }

    /**
     * Get authUrl.
     *
     * @return string|null
     */
    public function getAuthUrl()
    {
        return $this->authUrl;
    }

    /**
     * Set authLogin.
     *
     * @param string|null $authLogin
     *
     * @return Supplier
     */
    public function setAuthLogin($authLogin = null)
    {
        $this->authLogin = $authLogin;

        return $this;
    }

    /**
     * Get authLogin.
     *
     * @return string|null
     */
    public function getAuthLogin()
    {
        return $this->authLogin;
    }

    /**
     * Set authPassword.
     *
     * @param string|null $authPassword
     *
     * @return Supplier
     */
    public function setAuthPassword($authPassword = null)
    {
        $this->authPassword = $authPassword;

        return $this;
    }

    /**
     * Get authPassword.
     *
     * @return string|null
     */
    public function getAuthPassword()
    {
        return $this->authPassword;
    }

    /**
     * Set authComment.
     *
     * @param string|null $authComment
     *
     * @return Supplier
     */
    public function setAuthComment($authComment = null)
    {
        $this->authComment = $authComment;

        return $this;
    }

    /**
     * Get authComment.
     *
     * @return string|null
     */
    public function getAuthComment()
    {
        return $this->authComment;
    }

    /**
     * Set contractTill.
     *
     * @param \DateTime|null $contractTill
     *
     * @return Supplier
     */
    public function setContractTill($contractTill = null)
    {
        $this->contractTill = $contractTill;

        return $this;
    }

    /**
     * Get contractTill.
     *
     * @return \DateTime|null
     */
    public function getContractTill()
    {
        return $this->contractTill;
    }

    /**
     * Set contractUpdatedBy.
     *
     * @param int|null $contractUpdatedBy
     *
     * @return Supplier
     */
    public function setContractUpdatedBy($contractUpdatedBy = null)
    {
        $this->contractUpdatedBy = $contractUpdatedBy;

        return $this;
    }

    /**
     * Get contractUpdatedBy.
     *
     * @return int|null
     */
    public function getContractUpdatedBy()
    {
        return $this->contractUpdatedBy;
    }

    /**
     * Set geoPointId.
     *
     * @param int|null $geoPointId
     *
     * @return Supplier
     */
    public function setGeoPointId($geoPointId = null)
    {
        $this->geoPointId = $geoPointId;

        return $this;
    }

    /**
     * Get geoPointId.
     *
     * @return int|null
     */
    public function getGeoPointId()
    {
        return $this->geoPointId;
    }

    /**
     * Set orderThresholdSchedule.
     *
     * @param string|null $orderThresholdSchedule
     *
     * @return Supplier
     */
    public function setOrderThresholdSchedule($orderThresholdSchedule = null)
    {
        $this->orderThresholdSchedule = $orderThresholdSchedule;

        return $this;
    }

    /**
     * Get orderThresholdSchedule.
     *
     * @return string|null
     */
    public function getOrderThresholdSchedule()
    {
        return $this->orderThresholdSchedule;
    }

    /**
     * Set orderDeliverySchedule.
     *
     * @param string|null $orderDeliverySchedule
     *
     * @return Supplier
     */
    public function setOrderDeliverySchedule($orderDeliverySchedule = null)
    {
        $this->orderDeliverySchedule = $orderDeliverySchedule;

        return $this;
    }

    /**
     * Get orderDeliverySchedule.
     *
     * @return string|null
     */
    public function getOrderDeliverySchedule()
    {
        return $this->orderDeliverySchedule;
    }

    /**
     * Set orderThresholdTime.
     *
     * @param \DateTime|null $orderThresholdTime
     *
     * @return Supplier
     */
    public function setOrderThresholdTime($orderThresholdTime = null)
    {
        $this->orderThresholdTime = $orderThresholdTime;

        return $this;
    }

    /**
     * Get orderThresholdTime.
     *
     * @return \DateTime|null
     */
    public function getOrderThresholdTime()
    {
        return $this->orderThresholdTime;
    }

    /**
     * Set hasFreeDelivery.
     *
     * @param bool $hasFreeDelivery
     *
     * @return Supplier
     */
    public function setHasFreeDelivery($hasFreeDelivery)
    {
        $this->hasFreeDelivery = $hasFreeDelivery;

        return $this;
    }

    /**
     * Get hasFreeDelivery.
     *
     * @return bool
     */
    public function getHasFreeDelivery()
    {
        return $this->hasFreeDelivery;
    }

    /**
     * Set managerId.
     *
     * @param int|null $managerId
     *
     * @return Supplier
     */
    public function setManagerId($managerId = null)
    {
        $this->managerId = $managerId;

        return $this;
    }

    /**
     * Get managerId.
     *
     * @return int|null
     */
    public function getManagerId()
    {
        return $this->managerId;
    }

    /**
     * Set isActive.
     *
     * @param bool $isActive
     *
     * @return Supplier
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive.
     *
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
}
