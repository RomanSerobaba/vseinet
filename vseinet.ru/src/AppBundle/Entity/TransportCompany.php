<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TransportCompany.
 *
 * @ORM\Table(name="transport_company")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TransportCompanyRepository")
 */
class TransportCompany
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
     * @var int|null
     *
     * @ORM\Column(name="sort_order", type="integer", nullable=true)
     */
    private $sortOrder;

    /**
     * @var int|null
     *
     * @ORM\Column(name="tax", type="integer", nullable=true)
     */
    private $tax;

    /**
     * @var string|null
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code;

    /**
     * @var string|null
     *
     * @ORM\Column(name="calculator_url", type="string", length=255, nullable=true)
     */
    private $calculatorUrl;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return TransportCompany
     */
    public function setName($name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set sortOrder.
     *
     * @param int|null $sortOrder
     *
     * @return TransportCompany
     */
    public function setSortOrder($sortOrder = null): self
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    /**
     * Get sortOrder.
     *
     * @return int|null
     */
    public function getSortOrder(): ?int
    {
        return $this->sortOrder;
    }

    /**
     * Set tax.
     *
     * @param int|null $tax
     *
     * @return TransportCompany
     */
    public function setTax($tax = null): self
    {
        $this->tax = $tax;

        return $this;
    }

    /**
     * Get tax.
     *
     * @return int|null
     */
    public function getTax(): ?int
    {
        return $this->tax;
    }

    /**
     * Set url.
     *
     * @param string|null $url
     *
     * @return TransportCompany
     */
    public function setUrl($url = null): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url.
     *
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * Set isActive.
     *
     * @param bool $isActive
     *
     * @return TransportCompany
     */
    public function setIsActive($isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive.
     *
     * @return bool
     */
    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    /**
     * Set code.
     *
     * @param string $code
     *
     * @return TransportCompany
     */
    public function setCode($code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code.
     *
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Set calculatorUrl.
     *
     * @param string|null $calculatorUrl
     *
     * @return TransportCompany
     */
    public function setCalculatorUrl($calculatorUrl = null): self
    {
        $this->calculatorUrl = $calculatorUrl;

        return $this;
    }

    /**
     * Get calculatorUrl.
     *
     * @return string|null
     */
    public function getCalculatorUrl(): ?string
    {
        return $this->calculatorUrl;
    }
}
