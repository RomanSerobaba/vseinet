<?php 

namespace ContentBundle\Bus\Category\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Category;

class LinkCommandHandler extends MessageHandler
{
    public function handle(LinkCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository(Category::class)->find($command->id);
        if (!$category instanceof Category) {
            throw new NotFoundHttpException(sprintf('Категория %d не найдена', $command->id));
        }

        $main = $em->getRepository(Category::class)->find($command->linkToId);
        if (!$main instanceof Category) {
            throw new NotFoundHttpException(sprintf('Категория %d не найдена', $command->linkToId));
        }

        $q = $this->getDoctrine()->getManager()->createQuery("
            UPDATE ContentBundle:BaseProduct bp 
            SET bp.categoryId = :mainId
            WHERE bp.categoryId = :categoryId 
        ");
        $q->setParameter('mainId', $main->getId());
        $q->setParameter('categoryId', $category->getId());
        $q->execute();

        $category->setAliasForId($main->getId());   
     
        $em->persist($category);
        $em->flush();

        // bridge to old site
        $this->get('old_sound_rabbit_mq.execute.script_producer')->publish(json_encode([
            'url' => '/admin/bridge/category/link',
            'args' => $command->toArray(), 
        ]));
    }
}