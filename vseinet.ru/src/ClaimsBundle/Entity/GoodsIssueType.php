<?php

namespace ClaimsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GoodsIssueType
 *
 * @ORM\Table(name="goods_issue_type")
 * @ORM\Entity(repositoryClass="ClaimsBundle\Repository\GoodsIssueTypeRepository")
 */
class GoodsIssueType
{
    const CODE_LOST = 'lost';
    const CODE_FOUND = 'found';
    const CODE_DEFECT = 'defect';
    const CODE_DAMAGED = 'damaged';
    const CODE_INCOMPLETED = 'incompleted';
    const CODE_REGRADING = 'regrading';
    const CODE_REFUSED = 'refused';
    const CODE_UNDERLOADED = 'underloaded';
    const CODE_OVERLOADED = 'overloaded';

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
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_for_ordered", type="boolean")
     */
    private $isForOrdered;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_for_free", type="boolean")
     */
    private $isForFree;


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
     * Set code.
     *
     * @param string $code
     *
     * @return GoodsIssueType
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
     * Set name.
     *
     * @param string $name
     *
     * @return GoodsIssueType
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
     * Set isForOrdered.
     *
     * @param bool $isForOrdered
     *
     * @return GoodsIssueType
     */
    public function setIsForOrdered($isForOrdered)
    {
        $this->isForOrdered = $isForOrdered;

        return $this;
    }

    /**
     * Get isForOrdered.
     *
     * @return bool
     */
    public function getIsForOrdered()
    {
        return $this->isForOrdered;
    }

    /**
     * Set isForFree.
     *
     * @param bool $isForFree
     *
     * @return GoodsIssueType
     */
    public function setIsForFree($isForFree)
    {
        $this->isForFree = $isForFree;

        return $this;
    }

    /**
     * Get isForFree.
     *
     * @return bool
     */
    public function getIsForFree()
    {
        return $this->isForFree;
    }
}
