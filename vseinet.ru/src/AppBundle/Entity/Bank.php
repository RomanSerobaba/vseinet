<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Bank.
 *
 * @ORM\Table(name="bank")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BankRepository")
 */
class Bank
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
     * @ORM\Column(name="bic", type="string", length=9)
     */
    private $bic;

    /**
     * @var string
     *
     * @ORM\Column(name="correspondent_account", type="string", length=20)
     */
    private $correspondentAccount;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="short_name", type="string", length=255, nullable=true)
     */
    private $shortName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="index", type="string", length=6, nullable=true)
     */
    private $index;

    /**
     * @var int|null
     *
     * @ORM\Column(name="geo_city_id", type="integer", nullable=true)
     */
    private $geoCityId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="geo_address_id", type="integer", nullable=true)
     */
    private $geoAddressId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @var string|null
     *
     * @ORM\Column(name="okato", type="string", length=255, nullable=true)
     */
    private $okato;

    /**
     * @var string|null
     *
     * @ORM\Column(name="okpo", type="string", length=8, nullable=true)
     */
    private $okpo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="regnum", type="string", length=255, nullable=true)
     */
    private $regnum;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="issues_credit", type="boolean", nullable=true)
     */
    private $issuesCredit;

    /**
     * @var int|null
     *
     * @ORM\Column(name="counteragent_id", type="integer", nullable=true)
     */
    private $counteragentId;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_hidden", type="boolean", nullable=true)
     */
    private $isHidden;

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
     * Set bic.
     *
     * @param string $bic
     *
     * @return Bank
     */
    public function setBic($bic)
    {
        $this->bic = $bic;

        return $this;
    }

    /**
     * Get bic.
     *
     * @return string
     */
    public function getBic()
    {
        return $this->bic;
    }

    /**
     * Set correspondentAccount.
     *
     * @param string $correspondentAccount
     *
     * @return Bank
     */
    public function setCorrespondentAccount($correspondentAccount)
    {
        $this->correspondentAccount = $correspondentAccount;

        return $this;
    }

    /**
     * Get correspondentAccount.
     *
     * @return string
     */
    public function getCorrespondentAccount()
    {
        return $this->correspondentAccount;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Bank
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
     * Set shortName.
     *
     * @param string|null $shortName
     *
     * @return Bank
     */
    public function setShortName($shortName = null)
    {
        $this->shortName = $shortName;

        return $this;
    }

    /**
     * Get shortName.
     *
     * @return string|null
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * Set index.
     *
     * @param string|null $index
     *
     * @return Bank
     */
    public function setIndex($index = null)
    {
        $this->index = $index;

        return $this;
    }

    /**
     * Get index.
     *
     * @return string|null
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Set geoCityId.
     *
     * @param int|null $geoCityId
     *
     * @return Bank
     */
    public function setGeoCityId($geoCityId = null)
    {
        $this->geoCityId = $geoCityId;

        return $this;
    }

    /**
     * Get geoCityId.
     *
     * @return int|null
     */
    public function getGeoCityId()
    {
        return $this->geoCityId;
    }

    /**
     * Set geoAddressId.
     *
     * @param int|null $geoAddressId
     *
     * @return Bank
     */
    public function setGeoAddressId($geoAddressId = null)
    {
        $this->geoAddressId = $geoAddressId;

        return $this;
    }

    /**
     * Get geoAddressId.
     *
     * @return int|null
     */
    public function getGeoAddressId()
    {
        return $this->geoAddressId;
    }

    /**
     * Set phone.
     *
     * @param string|null $phone
     *
     * @return Bank
     */
    public function setPhone($phone = null)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone.
     *
     * @return string|null
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set okato.
     *
     * @param string|null $okato
     *
     * @return Bank
     */
    public function setOkato($okato = null)
    {
        $this->okato = $okato;

        return $this;
    }

    /**
     * Get okato.
     *
     * @return string|null
     */
    public function getOkato()
    {
        return $this->okato;
    }

    /**
     * Set okpo.
     *
     * @param string|null $okpo
     *
     * @return Bank
     */
    public function setOkpo($okpo = null)
    {
        $this->okpo = $okpo;

        return $this;
    }

    /**
     * Get okpo.
     *
     * @return string|null
     */
    public function getOkpo()
    {
        return $this->okpo;
    }

    /**
     * Set regnum.
     *
     * @param string|null $regnum
     *
     * @return Bank
     */
    public function setRegnum($regnum = null)
    {
        $this->regnum = $regnum;

        return $this;
    }

    /**
     * Get regnum.
     *
     * @return string|null
     */
    public function getRegnum()
    {
        return $this->regnum;
    }

    /**
     * Set issuesCredit.
     *
     * @param bool|null $issuesCredit
     *
     * @return Bank
     */
    public function setIssuesCredit($issuesCredit = null)
    {
        $this->issuesCredit = $issuesCredit;

        return $this;
    }

    /**
     * Get issuesCredit.
     *
     * @return bool|null
     */
    public function getIssuesCredit()
    {
        return $this->issuesCredit;
    }

    /**
     * Set counteragentId.
     *
     * @param int|null $counteragentId
     *
     * @return Bank
     */
    public function setCounteragentId($counteragentId = null)
    {
        $this->counteragentId = $counteragentId;

        return $this;
    }

    /**
     * Get counteragentId.
     *
     * @return int|null
     */
    public function getCounteragentId()
    {
        return $this->counteragentId;
    }

    /**
     * Set isHidden.
     *
     * @param bool|null $isHidden
     *
     * @return Bank
     */
    public function setIsHidden($isHidden = null)
    {
        $this->isHidden = $isHidden;

        return $this;
    }

    /**
     * Get isHidden.
     *
     * @return bool|null
     */
    public function getIsHidden()
    {
        return $this->isHidden;
    }
}
