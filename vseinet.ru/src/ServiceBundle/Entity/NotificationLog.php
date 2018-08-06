<?php

namespace ServiceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NotificationLog
 *
 * @ORM\Table(name="notification_log")
 * @ORM\Entity(repositoryClass="ServiceBundle\Repository\NotificationLogRepository")
 */
class NotificationLog
{
    const STATUS_CREATED = 'created';
    const STATUS_PENDING = 'pending';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_FAILED = 'failed';

    const SERVICE_NAME_SENDGRID = 'sendgrid';
    const SERVICE_NAME_REDSMS = 'redsms';
    const SERVICE_NAME_INFOBIP = 'infobip';

    const CHANNEL_SMS = 'sms';
    const CHANNEL_VIBER = 'viber';
    const CHANNEL_EMAIL = 'email';

    const TYPE_ACCOUNT_ACTIVATION = 'account_activation';
    const TYPE_SHIPMENT_CODE = 'shipment_code';
    const TYPE_APP_CANCELLATION = 'app_cancellation';
    const TYPE_SHIPMENT_CODE_EDITED = 'shipment_code_edited';
    const TYPE_CONTRACT = 'contract';
    const TYPE_DELIVERY_INFO = 'delivery_info';
    const TYPE_INVOICE = 'invoice';
    const TYPE_COMMERCIAL_PROPOSAL = 'commercial_proposal';
    const TYPE_RECONCILIATION_REPORT = 'reconciliation_report';
    const TYPE_COMMERCIAL_OFFER = 'commercial_offer';
    const TYPE_CUSTOM = 'custom';
    const TYPE_ORDER_ON_DELIVERY = 'order_on_delivery';
    const TYPE_NOT_REACHED = 'not_reached';
    const TYPE_PASSWORD_RECOVERY = 'password_recovery';
    const TYPE_CAN_BE_PAYED = 'can_be_payed';
    const TYPE_PREPAYMENT = 'prepayment';
    const TYPE_ORDER_SHIPPED = 'order_shipped';
    const TYPE_ORDER_CREATED = 'order_created';
    const TYPE_ORDER_ARRIVED = 'order_arrived';
    const TYPE_ORDER_REMINDER = 'order_reminder';
    const TYPE_PROMOTIONAL = 'promotional';
    const TYPE_SUPPLIER_RECLAMATION = 'supplier_reclamation';

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
     * @ORM\Column(name="pid", type="integer", nullable=true)
     */
    private $pid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var int
     *
     * @ORM\Column(name="order_id", type="integer", nullable=true)
     */
    private $orderId;

    /**
     * @var string
     *
     * @ORM\Column(name="channel", type="string", length=255)
     */
    private $channel;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="addressee", type="string", length=255)
     */
    private $addressee;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="string", length=255)
     */
    private $text;

    /**
     * @var string
     *
     * @ORM\Column(name="service_name", type="string", length=255)
     */
    private $serviceName;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="native_status", type="string", length=255, nullable=true)
     */
    private $nativeStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="native_error", type="string", length=255, nullable=true)
     */
    private $nativeError;

    /**
     * @var string
     *
     * @ORM\Column(name="native_id", type="string", length=255, nullable=true)
     */
    private $nativeId;

    /**
     * @var string
     *
     * @ORM\Column(name="temp_id", type="string", length=255, nullable=true)
     */
    private $tempId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var int
     *
     * @ORM\Column(name="amount", type="integer")
     */
    private $amount;


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
     * Set pid
     *
     * @param integer $pid
     *
     * @return NotificationLog
     */
    public function setPid($pid)
    {
        $this->pid = $pid;

        return $this;
    }

    /**
     * Get pid
     *
     * @return int
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return NotificationLog
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set orderId
     *
     * @param integer $orderId
     *
     * @return NotificationLog
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Get orderId
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set channel
     *
     * @param string $channel
     *
     * @return NotificationLog
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * Get channel
     *
     * @return string
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return NotificationLog
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set addressee
     *
     * @param string $addressee
     *
     * @return NotificationLog
     */
    public function setAddressee($addressee)
    {
        $this->addressee = $addressee;

        return $this;
    }

    /**
     * Get addressee
     *
     * @return string
     */
    public function getAddressee()
    {
        return $this->addressee;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return NotificationLog
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set serviceName
     *
     * @param string $serviceName
     *
     * @return NotificationLog
     */
    public function setServiceName($serviceName)
    {
        $this->serviceName = $serviceName;

        return $this;
    }

    /**
     * Get serviceName
     *
     * @return string
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return NotificationLog
     */
    public function setStatus($status)
    {
        $this->status = $status;

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
     * Set nativeStatus
     *
     * @param string $nativeStatus
     *
     * @return NotificationLog
     */
    public function setNativeStatus($nativeStatus)
    {
        $this->nativeStatus = $nativeStatus;

        return $this;
    }

    /**
     * Get nativeStatus
     *
     * @return string
     */
    public function getNativeStatus()
    {
        return $this->nativeStatus;
    }

    /**
     * Set nativeError
     *
     * @param string $nativeError
     *
     * @return NotificationLog
     */
    public function setNativeError($nativeError)
    {
        $this->nativeError = $nativeError;

        return $this;
    }

    /**
     * Get nativeError
     *
     * @return string
     */
    public function getNativeError()
    {
        return $this->nativeError;
    }

    /**
     * Set nativeId
     *
     * @param string $nativeId
     *
     * @return NotificationLog
     */
    public function setNativeId($nativeId)
    {
        $this->nativeId = $nativeId;

        return $this;
    }

    /**
     * Get nativeId
     *
     * @return string
     */
    public function getNativeId()
    {
        return $this->nativeId;
    }

    /**
     * Set tempId
     *
     * @param string $tempId
     *
     * @return NotificationLog
     */
    public function setTempId($tempId)
    {
        $this->tempId = $tempId;

        return $this;
    }

    /**
     * Get tempId
     *
     * @return string
     */
    public function getTempId()
    {
        return $this->tempId;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return NotificationLog
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set amount
     *
     * @param integer $amount
     *
     * @return NotificationLog
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }
}

