<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WholesaleContract.
 *
 * @ORM\Table(name="wholesale_contract")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WholesaleContractRepository")
 */
class WholesaleContract
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="contract_id_seq", initialValue=1)
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
     * @ORM\Column(name="contract_type_code", type="string", length=255)
     */
    private $contractTypeCode;

    /**
     * @var int
     *
     * @ORM\Column(name="financial_counteragent_id", type="integer")
     */
    private $financialCounteragentId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="signed_at", type="datetime")
     */
    private $signedAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="terminated_at", type="datetime", nullable=true)
     */
    private $terminatedAt;

    /**
     * @var float
     *
     * @ORM\Column(name="default_margin", type="float")
     */
    private $defaultMargin;

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
     * @return Contract
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
     * Set contractTypeCode.
     *
     * @param string $contractTypeCode
     *
     * @return Contract
     */
    public function setContractTypeCode($contractTypeCode)
    {
        $this->contractTypeCode = $contractTypeCode;

        return $this;
    }

    /**
     * Get contractTypeCode.
     *
     * @return string
     */
    public function getContractTypeCode()
    {
        return $this->contractTypeCode;
    }

    /**
     * Set financialCounteragentId.
     *
     * @param int $financialCounteragentId
     *
     * @return Contract
     */
    public function setFinancialCounteragentId($financialCounteragentId)
    {
        $this->financialCounteragentId = $financialCounteragentId;

        return $this;
    }

    /**
     * Get financialCounteragentId.
     *
     * @return int
     */
    public function getFinancialCounteragentId()
    {
        return $this->financialCounteragentId;
    }

    /**
     * Set signedAt.
     *
     * @param \DateTime $signedAt
     *
     * @return Contract
     */
    public function setSignedAt($signedAt)
    {
        $this->signedAt = $signedAt;

        return $this;
    }

    /**
     * Get signedAt.
     *
     * @return \DateTime
     */
    public function getSignedAt()
    {
        return $this->signedAt;
    }

    /**
     * Set terminatedAt.
     *
     * @param \DateTime|null $terminatedAt
     *
     * @return Contract
     */
    public function setTerminatedAt($terminatedAt = null)
    {
        $this->terminatedAt = $terminatedAt;

        return $this;
    }

    /**
     * Get terminatedAt.
     *
     * @return \DateTime|null
     */
    public function getTerminatedAt()
    {
        return $this->terminatedAt;
    }

    /**
     * Set defaultMargin.
     *
     * @param int $defaultMargin
     *
     * @return WholesaleContract
     */
    public function setDefaultMargin($defaultMargin)
    {
        $this->defaultMargin = $defaultMargin;

        return $this;
    }

    /**
     * Get defaultMargin.
     *
     * @return float
     */
    public function getDefaultMargin()
    {
        return $this->defaultMargin;
    }
}
