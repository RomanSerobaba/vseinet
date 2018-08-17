<?php

namespace AppBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Enum\UserRole;

/**
 * User
 *
 * @ORM\Table("`user`")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User implements UserInterface
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
     */
    private $username;

    /**
     * @var string
     * 
     * @ORM\Column(name="password", type="string")
     */
    private $password;

    /**
     * @var \DateTime
     * 
     * @ORM\Column(name="last_logined_at", type="datetime")
     */
    private $lastLoginedAt;

    /**
     * @var \DateTime
     * 
     * @ORM\Column(name="registered_at", type="datetime")
     */
    private $registeredAt;

    /**
     * @var int
     *
     * @ORM\Column(name="person_id", type="integer")
     */
    private $personId;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_city_id", type="integer")
     */
    private $geoCityId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_marketing_subscribed", type="boolean")
     */
    private $isMarketingSubscribed;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_transactional_subscribed", type="boolean")
     */
    private $isTransactionalSubscribed;

    /**
     * @var string[]
     */
    public $roles = [];

    /**
     * @var Person
     */
    public $person;

    /**
     * @var boolean
     */
    public $isFired;

    /**
     * @var \DateTime
     */
    public $clockInTime;

    /**
     * @var string
     */
    public $ipAddress;


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
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get id as username for security service
     * 
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set hashed password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set last logined time
     *
     * @param \DateTime $time
     *
     * @return User
     */
    public function setLastLoginedAt(\DateTime $time = null)
    {
        $this->lastLoginedAt = $time;

        return $this;
    }

    /**
     * Get last logined time
     *
     * @return \DateTime
     */
    public function getLastLoginedAt()
    {
        return $this->lastLoginedAt;
    }  

    /**
     * Set registered time
     *
     * @param \DateTime $time
     *
     * @return User
     */
    public function setRegisteredAt(\DateTime $time = null)
    {
        $this->registeredAt = $time;

        return $this;
    }

    /**
     * Get registered time
     *
     * @return \DateTime
     */
    public function getRegisteredAt()
    {
        return $this->registeredAt;
    }

    /**
     * Set personId
     *
     * @param integer $personId
     *
     * @return User
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;

        return $this;
    }

    /**
     * Get personId 
     *
     * @return int
     */
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     * Set geoCityId
     *
     * @param integer $geoCityId
     *
     * @return User
     */
    public function setGeoCityId($geoCityId)
    {
        $this->geoCityId = $geoCityId;

        return $this;
    }

    /**
     * Get geoCityId 
     *
     * @return int
     */
    public function getGeoCityId()
    {
        return $this->geoCityId;
    }

    /**
     * Set isMarketingSubscribed
     *
     * @param integer $isMarketingSubscribed
     *
     * @return User
     */
    public function setIsMarketingSubscribed($isMarketingSubscribed)
    {
        $this->isMarketingSubscribed = $isMarketingSubscribed;

        return $this;
    }

    /**
     * Get isMarketingSubscribed 
     *
     * @return int
     */
    public function getIsMarketingSubscribed()
    {
        return $this->isMarketingSubscribed;
    }

    /**
     * Set isTransactionalSubscribed
     *
     * @param integer $isTransactionalSubscribed
     *
     * @return User
     */
    public function setIsTransactionalSubscribed($isTransactionalSubscribed)
    {
        $this->isTransactionalSubscribed = $isTransactionalSubscribed;

        return $this;
    }

    /**
     * Get isTransactionalSubscribed 
     *
     * @return int
     */
    public function getIsTransactionalSubscribed()
    {
        return $this->isTransactionalSubscribed;
    }

    /**
     * @inheritdoc
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Check role
     * 
     * @param string $role
     * 
     * @return bool
     */
    public function isRole($role)
    {
        return in_array($role, $this->roles);
    }

    /**
     * Check is client 
     * 
     * @return bool 
     */
    public function isClient()
    {
        return $this->isRole(UserRole::CLIENT);
    }

    /**
     * Check is employee
     * 
     * @return bool
     */
    public function isEmployee()
    {
        return $this->isRole(UserRole::EMPLOYEE);
    }

    /**
     * Check is contenter
     * 
     * @return bool
     */
    public function isContenter()
    {
        return $this->isRole(UserRole::CONTENTER);
    }

    /**
     * @inheritdoc
     */
    public function getSalt()
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function eraseCredentials()
    {
        $this->person = null;
    }
}
