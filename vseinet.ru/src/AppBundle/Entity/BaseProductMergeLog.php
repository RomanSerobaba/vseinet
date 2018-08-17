<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BaseProductMergeLog
 *
 * @ORM\Table(name="base_product_merge_log")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BaseProductMergeLogRepository")
 */
class BaseProductMergeLog
{
    /**
     * @var int
     *
     * @ORM\Column(name="old_base_product_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $oldId;

    /**
     * @var int
     *
     * @ORM\Column(name="new_base_product_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $newId;


    /**
     * Set oldId
     *
     * @param integer $oldId
     *
     * @return BaseProductMergeLog
     */
    public function setOldId($oldId)
    {
        $this->oldId = $oldId;

        return $this;
    }

    /**
     * Get oldId
     *
     * @return int
     */
    public function getOldId()
    {
        return $this->oldId;
    }

    /**
     * Set newId
     *
     * @param integer $newId
     *
     * @return BaseProductMergeLog
     */
    public function setNewId($newId)
    {
        $this->newId = $newId;

        return $this;
    }

    /**
     * Get newId
     *
     * @return int
     */
    public function getNewId()
    {
        return $this->newId;
    }
}

