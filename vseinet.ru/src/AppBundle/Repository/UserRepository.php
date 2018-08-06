<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * UserRepository
 */
class UserRepository extends EntityRepository 
{
    public function completeLogin(UserInterface $user)
    {
        $em = $this->getEntityManager();

        $user->setLastLogin(new \DateTime());
        $em->persist($user);
        $em->flush();

        $query = $em->createNativeQuery("
            SELECT ar.code
            FROM user_to_acl_subrole AS utasr
            INNER JOIN acl_subrole AS asr ON asr.id = utasr.subrole_id
            INNER JOIN acl_role AS ar ON ar.id = asr.role_id
            WHERE utasr.user_id = :user_id
        ", new ResultSetMapping());
        $query->setParameter('user_id', $user->getId());
        $roles = $query->getResult('ListHydrator');



        $user->setRoles($roles);


    }
}
