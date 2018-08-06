<?php 

namespace ContentBundle\Bus\Task\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Task;

class DeleteCommandHandler extends MessageHandler
{
    public function handle(DeleteCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $task = $em->getRepository(Task::class)->findOneBy($command->toArray());
        if (!$task instanceof Task) {
            throw new NotFoundHttpException('Задание не найдено');
        }

        $em->remove($task);
        $em->flush();
    }
}
