<?php 

namespace ContentBundle\Bus\Task\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Manager;
use ContentBundle\Entity\Task;

class GetListQueryHandler extends MessageHandler
{
    public function handle(GetListQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $manager = $em->getRepository(Manager::class)->find($query->managerId);
        if (!$manager instanceof Manager) {
            throw new NotFoundHttpException(sprintf('Контент-манажер %s не найден', $query->managerId));
        }

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\Task\Query\DTO\Task (
                    t.id,
                    t.categoryId,
                    GROUP_CONCAT(c.name ORDER BY cp.plevel SEPARATOR ' / '),
                    cn.name
                )
            FROM ContentBundle:Task t 
            INNER JOIN ContentBundle:CategoryPath cp WITH cp.id = t.categoryId 
            INNER JOIN ContentBundle:Category c WITH c.id = cp.pid AND c.id != cp.id
            INNER JOIN ContentBundle:Category cn WITH cn.id = t.categoryId 
            WHERE t.managerId = :managerId AND c.id > 0
            GROUP BY t.id, cn.id
            ORDER BY cn.name
        ");
        $q->setParameter('managerId', $manager->getUserId());

        return $q->getArrayResult();
    }
}
