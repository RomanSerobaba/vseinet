<?php

namespace MatrixBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TradeMatrixTemplate
 *
 * @ORM\Table(name="trade_matrix_template")
 * @ORM\Entity(repositoryClass="MatrixBundle\Repository\TradeMatrixTemplateRepository")
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
     * @ORM\Column(name="name", type="string", unique=true)
     */
    private $name;

    /**
     * @var \Date|null
     *
     * @ORM\Column(name="active_from", type="date", nullable=true)
     */
    private $activeFrom;

    /**
     * @var \Date|null
     *
     * @ORM\Column(name="active_till", type="date", nullable=true)
     */
    private $activeTill;


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
     * @param \Date|null $activeFrom
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
     * @return \Date|null
     */
    public function getActiveFrom()
    {
        return $this->activeFrom;
    }

    /**
     * Set activeTill.
     *
     * @param \Date|null $activeTill
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
     * @return \Date|null
     */
    public function getActiveTill()
    {
        return $this->activeTill;
    }
}
