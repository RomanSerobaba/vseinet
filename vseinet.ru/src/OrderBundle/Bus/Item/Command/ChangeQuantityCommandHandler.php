<?php 

namespace OrderBundle\Bus\Item\Command;

use AppBundle\Bus\Message\MessageHandler;
use ServiceBundle\Services\OrderService;
use SupplyBundle\Component\OrderComponent;
use ReservesBundle\Entity\GoodsRequest;
use OrderBundle\Entity\OrderItem;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ChangeQuantityCommandHandler extends MessageHandler
{
    public function handle(ChangeQuantityCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $class = $command->type === OrderComponent::TYPE_ORDER ? OrderItem::class : GoodsRequest::class;
        $repository = $em->getRepository($class);

        if ($command->quantity <= 0) {
            throw new BadRequestHttpException('Недопустимое количество');
        }

        /**
         * @var OrderItem $model
         */
        $model = $repository->find($command->id);

        if (!empty($model)) {
            /**
             * @var OrderService $service
             */
            $service = $this->get('service.order');
            if ($command->type === OrderComponent::TYPE_ORDER) {
                $service->changeItemQuantity($command->id, $command->quantity);
            } else {
                $service->changeRequestQuantity($command->id, $command->quantity);
            }
        } else {
            throw new NotFoundHttpException();
        }
    }
}