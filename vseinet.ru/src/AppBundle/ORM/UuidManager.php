<?php 

namespace AppBundle\ORM;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\UuidGenerator;

class UuidManager
{
    const EXPIRES_IN = 300;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function generate()
    {
        return (new UuidGenerator())->generate($this->em, null);
    }

    public function saveId($uuid, $id)
    {
        $smtp = $this->em->getConnection()->prepare("INSERT INTO uuid_manager VALUES (:uuid, :id, :expires_at)");
        $smtp->execute(['uuid' => $uuid, 'id' => $id, 'expires_at' => date('Y-m-d H:i:s', time() + self::EXPIRES_IN)]);
    }

    public function loadId($uuid)
    {
        $smtp = $this->em->getConnection()->prepare("SELECT id FROM uuid_manager WHERE uuid = :uuid");
        $smtp->execute(['uuid' => $uuid]);
        $id = $smtp->fetchColumn();
        $smtp = $this->em->getConnection()->prepare("DELETE FROM uuid_manager WHERE uuid = :uuid");
        $smtp->execute(['uuid' => $uuid]);

        return $id;
    }

    public function deleteExpired()
    {
        $smtp = $this->em->getConnection()->prepare("DELETE FROM uuid_manager WHERE expires_at < :expires_at");
        $smtp->execute(['expires_at' => date('Y-m-d H:i:s', time())]);    
    }
}