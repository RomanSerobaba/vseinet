<?php

namespace OrgBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActivityObject
 *
 * @ORM\Table(name="org_activity_object")
 * @ORM\Entity(repositoryClass="OrgBundle\Repository\ActivityObjectRepository")
 */
class ActivityObject
{
    const CODE_AllOrders         = 'AllOrders';
    const CODE_ClientsOrders     = 'ClientOrders';
    const CODE_WholesalersOrders = 'WholesalersOrders';
    const CODE_ManagedOrders     = 'ManagedOrders';
    const CODE_OverstockedGoods  = 'OverstockedGoods';
    const CODE_Reclamations      = 'Reclamations';

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
     * @ORM\Column(name="name", type="string", length=200)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=100)
     */
    private $code;

    /**
     * @var bool
     *
     * @ORM\Column(name="can_be_filtered_by_category", type="boolean")
     */
    private $canBeFilteredByCategory;

    /**
     * @var bool
     *
     * @ORM\Column(name="has_interval", type="boolean")
     */
    private $hasInterval;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_negative", type="boolean")
     */
    private $isNegative;


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
     * @return ActivityObject
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
     * Set code.
     *
     * @param string $code
     *
     * @return ActivityObject
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code.
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set canBeFilteredByCategory.
     *
     * @param bool $canBeFilteredByCategory
     *
     * @return ActivityObject
     */
    public function setCanBeFilteredByCategory($canBeFilteredByCategory)
    {
        $this->canBeFilteredByCategory = $canBeFilteredByCategory;

        return $this;
    }

    /**
     * Get canBeFilteredByCategory.
     *
     * @return bool
     */
    public function getCanBeFilteredByCategory()
    {
        return $this->canBeFilteredByCategory;
    }

    /**
     * Set hasInterval.
     *
     * @param bool $hasInterval
     *
     * @return ActivityObject
     */
    public function setHasInterval($hasInterval)
    {
        $this->hasInterval = $hasInterval;

        return $this;
    }

    /**
     * Get hasInterval.
     *
     * @return bool
     */
    public function getHasInterval()
    {
        return $this->hasInterval;
    }

    /**
     * Set isNegative.
     *
     * @param bool $isNegative
     *
     * @return ActivityObject
     */
    public function setIsNegative($isNegative)
    {
        $this->isNegative = $isNegative;

        return $this;
    }

    /**
     * Get isNegative.
     *
     * @return bool
     */
    public function getIsNegative()
    {
        return $this->isNegative;
    }
}
