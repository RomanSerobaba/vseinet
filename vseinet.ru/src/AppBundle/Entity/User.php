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
    private $cityId;

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
     * @var string
     */
    public $name;

    /**
     * @var array<string>
     */
    public $roles = [];

    /**
     * @var array<string>
     */
    public $rules = [];

    /**
     * @var UserData
     */
    public $data;

    /**
     * @var Person
     */
    public $person;

    /**
     * @var Contact[]
     */
    public $contacts;

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
     * Get id as username for security service
     * 
     * @return string
     */
    public function getUsername()
    {
        return strval($this->id);
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
     * Set cityId
     *
     * @param integer $cityId
     *
     * @return User
     */
    public function setCityId($cityId)
    {
        $this->cityId = $cityId;

        return $this;
    }

    /**
     * Get cityId 
     *
     * @return int
     */
    public function getCityId()
    {
        return $this->cityId;
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
     * Check role
     * 
     * @param string $role
     * 
     * @return boolean
     */
    public function hasRole($role)
    {
        return in_array($role, $this->roles);
    }

    /**
     * Check is employee
     *
     * @return boolean
     */
    public function isEmployee()
    {
        return !empty($this->roles) 
            && !$this->hasRole(UserRole::CLIENT) 
            && !$this->hasRole(UserRole::WHOLESALER) 
            && !$this->hasRole(UserRole::FRANCHISER);    
    }

    /**
     * Check rule
     * 
     * @param string $rule
     * 
     * @return boolean
     */
    public function hasRule($rule)
    {
        return in_array($rule, $this->rules);
    }

    /**
     * @inheritdoc
     */
    public function getRoles()
    {
        return $this->roles;
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

    }
}
