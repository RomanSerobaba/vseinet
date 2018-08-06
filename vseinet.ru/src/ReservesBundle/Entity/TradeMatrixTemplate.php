<?php

namespace ReservesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TradeMatrixTemplate
 *
 * @ORM\Table(name="trade_matrix_template")
 * @ORM\Entity(repositoryClass="ReservesBundle\Repository\TradeMatrixTemplateRepository")
 */
class TradeMatrixTemplate
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
     * @var \DateTime|null
     *
     * @ORM\Column(name="active_from", type="date", nullable=true)
     */
    private $activeFrom;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="active_till", type="date", nullable=true)
     */
    private $activeTill;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_removable", type="boolean")
     */
    private $isRemovable;


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
     * @return TradeMatrixTemplate
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
     * Set activeFrom.
     *
     * @param \DateTime|null $activeFrom
     *
     * @return TradeMatrixTemplate
     */
    public function setActiveFrom($activeFrom = null)
    {
        $this->activeFrom = $activeFrom;

        return $this;
    }

    /**
     * Get activeFrom.
     *
     * @return \DateTime|null
     */
    public function getActiveFrom()
    {
        return $this->activeFrom;
    }

    /**
     * Set activeTill.
     *
     * @param \DateTime|null $activeTill
     *
     * @return TradeMatrixTemplate
     */
    public function setActiveTill($activeTill = null)
    {
        $this->activeTill = $activeTill;

        return $this;
    }

    /**
     * Get activeTill.
     *
     * @return \DateTime|null
     */
    public function getActiveTill()
    {
        return $this->activeTill;
    }

    /**
     * Set isRemovable.
     *
     * @param bool $isRemovable
     *
     * @return TradeMatrixTemplate
     */
    public function setIsRemovable($isRemovable)
    {
        $this->isRemovable = $isRemovable;

        return $this;
    }

    /**
     * Get isRemovable.
     *
     * @return bool
     */
    public function getIsRemovable()
    {
        return $this->isRemovable;
    }
}
