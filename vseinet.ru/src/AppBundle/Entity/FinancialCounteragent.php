<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Table(name="financial_counteragent")
 * @ORM\Entity
 */
class FinancialCounteragent
{
    /*
     * Поля
     */

    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(name="counteragent_id", type="integer", nullable=true)
     */
    private $counteragentId;

    /**
     * @var int
     * @ORM\Column(name="user_id", type="integer", nullable=true)
     */
    private $userId;

    /**
     * @var int
     * @ORM\Column(name="comuser_id", type="integer", nullable=true)
     */
    private $comuserId;

    /**
     * @var string
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var bool
     * @ORM\Column(name="is_supplier", type="boolean")
     */
    private $isSupplier;

    /**
     * @var bool
     * @ORM\Column(name="is_buyer", type="boolean")
     */
    private $isBuyer;

    /**
     * @var bool
     * @ORM\Column(name="is_bank", type="boolean")
     */
    private $isBank;

    /**
     * @var bool
     * @ORM\Column(name="is_employee", type="boolean")
     */
    private $isEmployee;

    /**
     * @var bool
     * @ORM\Column(name="is_service_center", type="boolean")
     */
    private $isServiceCenter;

    /**
     * @var \DateTime
     * @ORM\Column(name="last_mutual_settlement_is_checked", type="datetime", nullable=true)
     */
    private $lastMutualSettlementIsChecked;

    // </editor-fold>
    // <editor-fold defaultstate="collapsed" desc="Методы">

    /*
     * Методы
     */

    /**
     * Получить идентификатор
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Получить идентификатор контрагента.
     *
     * @return int|null
     */
    public function getCounteragentId()
    {
        return $this->counteragentId;
    }

    /**
     * Установить идентификатор контрагента.
     *
     * @param int|null $counteragentId
     *
     * @return FinancialCounteragent
     */
    public function setCounteragentId($counteragentId = null)
    {
        $this->counteragentId = $counteragentId;

        return $this;
    }

    /**
     * Получить наименование.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Установить наименование.
     *
     * @param string $name
     *
     * @return FinancialCounteragent
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Получить идентификатор пользователя.
     *
     * @return string|null
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Установить идентификатор пользователя.
     *
     * @param string|null $userId
     *
     * @return FinancialCounteragent
     */
    public function setUserId($userId = null)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Получить идентификатор пользователя.
     *
     * @return string|null
     */
    public function getComuserId()
    {
        return $this->comuserId;
    }

    /**
     * Установить идентификатор пользователя.
     *
     * @param string|null $comuserId
     *
     * @return FinancialCounteragent
     */
    public function setComuserId($comuserId = null)
    {
        $this->comuserId = $comuserId;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsBank()
    {
        return $this->isBank;
    }

    /**
     * @param bool $isBank
     *
     * @return FinancialCounteragent
     */
    public function setIsBank($isBank = false)
    {
        $this->isBank = $isBank;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsBuyer()
    {
        return $this->isBuyer;
    }

    /**
     * @param bool $isBuyer
     *
     * @return FinancialCounteragent
     */
    public function setIsBuyer($isBuyer = false)
    {
        $this->isBuyer = $isBuyer;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsEmployee()
    {
        return $this->isEmployee;
    }

    /**
     * @param bool $isEmployee
     *
     * @return FinancialCounteragent
     */
    public function setIsEmployee($isEmployee = false)
    {
        $this->isEmployee = $isEmployee;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsSupplier()
    {
        return $this->isSupplier;
    }

    /**
     * @param bool $isSupplier
     *
     * @return FinancialCounteragent
     */
    public function setIsSupplier($isSupplier = false)
    {
        $this->isSupplier = $isSupplier;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsServiceCenter()
    {
        return $this->isServiceCenter;
    }

    /**
     * @param bool $isServiceCenter
     *
     * @return FinancialCounteragent
     */
    public function setIsServiceCenter($isServiceCenter = false)
    {
        $this->isServiceCenter = $isServiceCenter;

        return $this;
    }

    /**
     * Получить дату.
     *
     * @return \DateTime
     */
    public function getLastMutualSettlementIsChecked()
    {
        return $this->lastMutualSettlementIsChecked;
    }

    /**
     * Установить дату.
     *
     * @param \DateTime $lastMutualSettlementIsChecked
     *
     * @return FinancialCounteragent
     */
    public function setLastMutualSettlementIsChecked($lastMutualSettlementIsChecked)
    {
        $this->lastMutualSettlementIsChecked = $lastMutualSettlementIsChecked;

        return $this;
    }

    // </editor-fold>
}
