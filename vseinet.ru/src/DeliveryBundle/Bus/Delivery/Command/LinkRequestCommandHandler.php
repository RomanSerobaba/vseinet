<?php 

namespace DeliveryBundle\Bus\Delivery\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use DeliveryBundle\Entity\DeliveryDoc;
use DeliveryBundle\Entity\OrderDelivery;
use DeliveryBundle\Entity\DeliveryItem;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class LinkRequestCommandHandler extends MessageHandler
{
    public function handle(LinkRequestCommand $command)
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

            if ($request->getType() != $delivery->getType()) {
                throw new NotFoundHttpException(sprintf('Тип доставки у заявки %d не совпадает с типом целевой доставки', $requestId));
            }

            $link = $em->getRepository(DeliveryItem::class)->findOneBy(['orderDeliveryId' => $requestId]);

            if ($link instanceof DeliveryItem) {
                throw new NotFoundHttpException(sprintf('Заявка %d уже добавлена в доставку %d', $requestId, $link->getDeliveryId()));
            }

            $link = new DeliveryItem();
            $link->setDeliveryId($command->id);
            $link->setOrderDeliveryId($requestId);
            $em->persist($link);
        }
    }
}