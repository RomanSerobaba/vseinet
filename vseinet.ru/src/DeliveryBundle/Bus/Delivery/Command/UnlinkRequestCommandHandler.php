<?php 

namespace DeliveryBundle\Bus\Delivery\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use DeliveryBundle\Entity\DeliveryDoc;
use DeliveryBundle\Entity\OrderDelivery;
use DeliveryBundle\Entity\DeliveryItem;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UnlinkRequestCommandHandler extends MessageHandler
{
    public function handle(UnlinkRequestCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $delivery = $em->getRepository(DeliveryDoc::class)->findOneBy(['number' => $command->id]);
        if (!$delivery instanceof DeliveryDoc) {
            throw new NotFoundHttpException(sprintf('Доставка %d не найдена', $command->id));
        }

        if ('new' != $delivery->getStatusCode()) {
            throw new BadRequestHttpException(sprintf('Изменение заявок в доставке не возможно после ее отгрузки', $command->id));
        }

        foreach ($command->requestsIds as $requestId) {
            $request = $em->getRepository(OrderDelivery::class)->find($requestId);

            if (!$request instanceof OrderDelivery) {
                throw new NotFoundHttpException(sprintf('Заявка %d не найдена', $requestId));
            }

            $link = $em->getRepository(DeliveryItem::class)->findOneBy(['orderDeliveryId' => $requestId, 'deliveryId' => $command->id]);
            $em->remove($link);
        }
    }
}