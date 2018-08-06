<?php

namespace ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SMLMarkdownReason
 *
 * @ORM\Table(name="sml_markdown_reason")
 * @ORM\Entity(repositoryClass="ContentBundle\Repository\SMLMarkdownReasonRepository")
 */
class SMLMarkdownReason
{
    /**
     * @var string
     *
     * @ORM\Column(name="hash", type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $hash;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string")
     */
    private $value;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_disabled", type="boolean")
     */
    private $isDisabled;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_verified", type="boolean")
     */
    private $isVerified;


    /**
     * Set hash
     *
     * @param string $hash
     *
     * @return SMLMarkdownReason
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return SMLMarkdownReason
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set isDisabled
     *
     * @param boolean $isDisabled
     *
     * @return SMLMarkdownReason
     */
    public function setIsDisabled($isDisabled)
    {
        $this->isDisabled = $isDisabled;

        return $this;
    }

    /**
     * Get isDisabled
     *
     * @return bool
     */
    public function getIsDisabled()
    {
        return $this->isDisabled;
    }

    /**
     * Set isVerified
     *
     * @param boolean $isVerified
     *
     * @return SMLMarkdownReason
     */
    public function setIsVerified($isVerified)
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * Get isVerified
     *
     * @return bool
     */
    public function getIsVerified()
    {
        return $this->isVerified;
    }
}

