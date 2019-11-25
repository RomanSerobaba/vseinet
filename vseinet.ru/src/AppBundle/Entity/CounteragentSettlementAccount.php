<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CounteragentSettlementAccount.
 *
 * @ORM\Table(name="counteragent_settlement_account")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CounteragentSettlementAccountRepository")
 */
class CounteragentSettlementAccount
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="number", type="string", length=20, unique=true)
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
     * @return CounteragentSettlementAccount
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
     * @return CounteragentSettlementAccount
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
     * @return CounteragentSettlementAccount
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
     * @return CounteragentSettlementAccount
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
