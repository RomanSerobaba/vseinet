<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CompanyToFinancialCounteragent.
 *
 * @ORM\Table(name="company_to_financial_counteragent")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CompanyToFinancialCounteragentRepository")
 */
class CompanyToFinancialCounteragent
{
    /**
     * @var int
     *
     * @ORM\Column(name="company_id", type="integer")
     * @ORM\Id
     */
    private $companyId;

    /**
     * @var int
     *
     * @ORM\Column(name="financial_counteragent_id", type="integer")
     * @ORM\Id
     */
    private $financialCounteragentId;

    /**
     * @var string
     *
     * @ORM\Column(name="short_name", type="string", length=255)
     */
    private $shortName;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_default", type="boolean")
     */
    private $isDefault;

    /**
     * Set companyId.
     *
     * @param int $companyId
     *
     * @return CompanyToFinancialCounteragent
     */
    public function setCompanyId($companyId)
    {
        $this->companyId = $companyId;

        return $this;
    }

    /**
     * Get companyId.
     *
     * @return int
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * Set financialCounteragentId.
     *
     * @param int $financialCounteragentId
     *
     * @return CompanyToFinancialCounteragent
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
     * Set shortName.
     *
     * @param string $shortName
     *
     * @return CompanyToFinancialCounteragent
     */
    public function setShortName($shortName)
    {
        $this->shortName = $shortName;

        return $this;
    }

    /**
     * Get shortName.
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * Set isActive.
     *
     * @param bool $isActive
     *
     * @return CompanyToFinancialCounteragent
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

    /**
     * Set isDefault.
     *
     * @param bool $isDefault
     *
     * @return CompanyToFinancialCounteragent
     */
    public function setIsDefault($isDefault)
    {
        $this->isDefault = $isDefault;

        return $this;
    }

    /**
     * Get isDefault.
     *
     * @return bool
     */
    public function getIsDefault()
    {
        return $this->isDefault;
    }
}
