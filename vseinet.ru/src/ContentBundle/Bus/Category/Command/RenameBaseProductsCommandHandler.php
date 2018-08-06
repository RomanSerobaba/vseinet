<?php 

namespace ContentBundle\Bus\Category\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ContentBundle\Entity\Category;

class RenameBaseProductsCommandHandler extends MessageHandler 
{
    public function handle(RenameBaseProductsCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository(Category::class)->find($command->id);
        if (!$category instanceof Category) {
            throw new NotFoundHttpException(sprintf('Категория %d не найдена', $command->id));
        }

        $this->get('old_sound_rabbit_mq.execute.command_producer')->publish(json_encode([
            'command' => 'rename:base:products',
            'args' => [
                'criteria' => [
                    'categoryId' => $category->getId(),
                ],
            ],
        ]));
    }
}