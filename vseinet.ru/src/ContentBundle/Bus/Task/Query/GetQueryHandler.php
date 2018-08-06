<?php 

namespace ContentBundle\Bus\Task\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Task;

/**
 * @deprecated
 */
class GetQueryHandler extends MessageHandler
{
    public function handle(GetQuery $query)
    {
        $task = $this->getDoctrine()->getManager()->getRepository(Task::class)->find($query->id);
        if (!$task instanceof Task) {
            throw new NotFoundHttpException(sprintf('Задание %s не найдено', $query->id));
        }

        return $task;
    }
}
