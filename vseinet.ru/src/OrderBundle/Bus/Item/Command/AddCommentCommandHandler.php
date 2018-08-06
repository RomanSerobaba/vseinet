<?php

namespace OrderBundle\Bus\Item\Command;

use AppBundle\Bus\Message\MessageHandler;
use OrderBundle\Entity\OrderItem;
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
         * @var OrderItem $model
         */
        $model = $em->getRepository(OrderItem::class)->find($command->id);

        if (!$model) {
            throw new NotFoundHttpException('Order item not found');
        }

        /**
         * @var \ServiceBundle\Services\OrderService $serviceOrder
         */
        $serviceOrder = $this->get('service.order');
        $id = $serviceOrder->addComment($model->getOrderId(), $command->text, $command->id, $command->isImportant);

        $this->get('uuid.manager')->saveId($command->uuid, $id);
    }
}