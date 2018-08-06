<?php

namespace AccountingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SettlementAccount
 *
 * @ORM\Table(name="settlement_account")
 * @ORM\Entity(repositoryClass="AccountingBundle\Repository\SettlementAccountRepository")
 */
class SettlementAccount
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="financial_resource_id_seq", initialValue=1)
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="number", type="string", length=255)
     */
    private $number;

    /**
     * @var int
     *
     * @ORM\Column(name="bank_id", type="integer")
     */
    private $bankId;

    /**
     * @var int
     *
     * @ORM\Column(name="counteragent_id", type="integer")
     */
    private $counteragentId;


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
     * Set title.
     *
     * @param string $title
     *
     * @return SettlementAccount
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set number.
     *
     * @param string $number
     *
     * @return SettlementAccount
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number.
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set bankId.
     *
     * @param int $bankId
     *
     * @return SettlementAccount
     */
    public function setBankId($bankId)
    {
        $this->bankId = $bankId;

        return $this;
    }

    /**
     * Get bankId.
     *
     * @return int
     */
    public function getBankId()
    {
        return $this->bankId;
    }

    /**
     * Set counteragentId.
     *
     * @param int $counteragentId
     *
     * @return SettlementAccount
     */
    public function setCounteragentId($counteragentId)
    {
        $this->counteragentId = $counteragentId;

        return $this;
    }

    /**
     * Get counteragentId.
     *
     * @return int
     */
    public function getCounteragentId()
    {
        return $this->counteragentId;
    }
}
