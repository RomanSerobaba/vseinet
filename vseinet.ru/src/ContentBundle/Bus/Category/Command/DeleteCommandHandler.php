<?php 

namespace ContentBundle\Bus\Category\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ContentBundle\Entity\Category;

class DeleteCommandHandler extends MessageHandler
{
    public function handle(DeleteCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository(Category::class)->find($command->id);
        if (!$category instanceof Category) {
            throw new NotFoundHttpException(sprintf('Категория %d не найдена', $command->id));
        }

        $q = $em->createQuery("
            SELECT 1
            FROM ContentBundle:BaseProduct bp 
            LEFT OUTER JOIN OrderBundle:OrderItem oi WITH oi.baseProductId = bp.id 
            LEFT OUTER JOIN SupplyBundle:SupplyItem si WITH si.baseProductId = bp.id
            WHERE bp.categoryId = :categoryId AND (oi.id IS NOT NULL OR si.id IS NOT NULL) 
        ");
        $q->setParameter('categoryId', $category->getId());
        $q->setMaxResults(1);
        if ($q->getOneOrNullResult()) {
            throw new BadRequestHttpException(sprintf('Категорию "%s" нельзя удалить', $category->getName()));
        }

        $em->remove($category);
        $em->flush();

        // bridge to old site
        $this->get('old_sound_rabbit_mq.execute.script_producer')->publish(json_encode([
            'url' => '/admin/bridge/category/remove',
            'args' => $command->toArray(), 
        ]));
    }
}