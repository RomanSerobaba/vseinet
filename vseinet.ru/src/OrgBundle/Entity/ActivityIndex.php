<?php

namespace OrgBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActivityIndex
 *
 * @ORM\Table(name="org_activity_index")
 * @ORM\Entity(repositoryClass="OrgBundle\Repository\ActivityIndexRepository")
 */
class ActivityIndex
{
    const CODE_QUANTITY = 'quantity';
    const CODE_SUPPLY   = 'supply';
    const CODE_SELLING  = 'selling';
    const CODE_MARGIN   = 'margin';

    const MEASURE_PIECE = 'piece';
    const MEASURE_MONEY = 'money';

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
     * @var string
     *
     * @ORM\Column(name="measure", type="string")
     */
    private $measure;


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
     * @return ActivityIndex
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
     * @return ActivityIndex
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
     * Set measure.
     *
     * @param string $measure
     *
     * @return ActivityIndex
     */
    public function setMeasure($measure)
    {
        $this->measure = $measure;

        return $this;
    }

    /**
     * Get measure.
     *
     * @return string
     */
    public function getMeasure()
    {
        return $this->measure;
    }
}
