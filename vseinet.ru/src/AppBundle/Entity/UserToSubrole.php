<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserToSubrole.
 *
 * @ORM\Table(name="user_to_acl_subrole")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserToSubroleRepository")
 */
class UserToSubrole
{
    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $userId;

    /**
     * @var int
     *
     * @ORM\Column(name="acl_subrole_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $subroleId;

    /**
     * Set userId.
     *
     * @param int $userId
     *
     * @return UserToSubrole
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
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

    /**
     * Set subroleId.
     *
     * @param int $subroleId
     *
     * @return UserToSubrole
     */
    public function setSubroleId($subroleId)
    {
        $this->subroleId = $subroleId;

        return $this;
    }

    /**
     * Get subroleId.
     *
     * @return int
     */
    public function getSubroleId()
    {
        return $this->subroleId;
    }
}
