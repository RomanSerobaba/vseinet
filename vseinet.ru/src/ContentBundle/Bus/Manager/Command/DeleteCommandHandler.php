<?php 

namespace ContentBundle\Bus\Manager\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Manager;
use Doctrine\ORM\NoResultException;

class DeleteCommandHandler extends MessageHandler
{
    public function handle(DeleteCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $manager = $em->getRepository(Manager::class)->find($command->id);
        if (!$manager instanceof Manager) {
            throw new NotFoundHttpException(sprintf('Контент-менеджер %d не найден', $command->UserId));
        }

        $isContenter = true;
        $q = $em->createQuery("
            SELECT r.code 
            FROM AppBundle:Role r 
            INNER JOIN AppBundle:Subrole sr WITH sr.roleId = r.id 
            INNER JOIN AppBundle:UserToSubrole u2sr WITH u2sr.subroleId = sr.id 
            WHERE u2sr.userId = :userId AND r.code = 'CONTENTER'
        ");
        $q->setParameter('userId', $manager->getUserId());
        try {
            $q->getSingleScalarResult();
        } 
        catch (NoResultException $e) {
            $isContenter = false;
        }

        if ($isContenter) {
            $manager->setGroupId(null);
        }
        else {
            $q = $em->createQuery("
                DELETE FROM ContentBundle:Task t
                WHERE t.managerId = :managerId 
            ");
            $q->setParameter('managerId', $manager->getUserId());
            $q->execute();

            $manager->setIsActive(false);
        }

        $em->persist($manager);
        $em->flush();
    }
}