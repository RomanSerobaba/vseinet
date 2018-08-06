<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ContactType
 *
 * @ORM\Table(name="contact_type")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ContactTypeRepository")
 */
class ContactType
{
    const CODE_PHONE = 'phone';
    const CODE_EMAIL = 'email';
    const CODE_MOBILE = 'mobile';
    const CODE_ICQ = 'icq';
    const CODE_SKYPE = 'skype';
    const CODE_CUSTOM = 'custom';

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="icon", type="string")
     */
    private $icon;


    /**
     * Set code
     *
     * @param string $code
     *
     * @return ContactType
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return ContactType
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set icon
     *
     * @param string $icon
     *
     * @return ContactType
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get icon
     *
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }
}

