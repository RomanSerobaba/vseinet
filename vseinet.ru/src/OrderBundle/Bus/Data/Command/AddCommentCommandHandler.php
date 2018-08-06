<?php

namespace OrderBundle\Bus\Data\Command;

use AppBundle\Bus\Message\MessageHandler;
use OrderBundle\Entity\OrderTable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AddCommentCommandHandler extends MessageHandler
{
    public function handle(AddCommentCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var OrderTable $model
         */
        $model = $em->getRepository(OrderTable::class)->find($command->id);

        if (!$model) {
            throw new NotFoundHttpException('Order not found');
        }

        /**
         * @var \ServiceBundle\Services\OrderService $serviceOrder
         */
        $serviceOrder = $this->get('service.order');
        $id = $serviceOrder->addComment($command->id, $command->text, null, $command->isImportant);

        $this->get('uuid.manager')->saveId($command->uuid, $id);
    }
}