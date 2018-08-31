<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="financial_counteragent")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FinancialCounteragentRepository")
 */
class FinancialCounteragent
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
     * @var int
     * 
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

 
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
     * Get userId.
     * 
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }
}
