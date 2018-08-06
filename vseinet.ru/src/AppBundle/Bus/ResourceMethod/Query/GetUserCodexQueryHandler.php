<?php 

namespace AppBundle\Bus\ResourceMethod\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\User;

class GetUserCodexQueryHandler extends MessageHandler
{
    public function handle(GetUserCodexQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository(User::class)->find($query->userId);
        if (!$user instanceof User) {
            throw new NotFoundHttpException(sprintf('Пользователь %d не найден', $query->userId));
        }

        $q = $em->createQuery("
            SELECT 
                NEW AppBundle\Bus\ResourceMethod\Query\DTO\UserCodexItem (
                    rm.id,
                    CASE WHEN EXISTS (
                        SELECT 1
                        FROM AppBundle:ApiMethodUserCodex amuc
                        WHERE amuc.resourceId = r.id AND amuc.userId = :userId 
                    ) THEN true ELSE false END
                )
            FROM AppBundle:ResourceMethod rm
        ");
        $q->setParameter('userId', $user->getId());

        return $q->getArrayResult();
    }
}