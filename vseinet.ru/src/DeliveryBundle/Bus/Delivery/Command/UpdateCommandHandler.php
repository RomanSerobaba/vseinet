<?php 

namespace DeliveryBundle\Bus\Delivery\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use DeliveryBundle\Entity\DeliveryDoc;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UpdateCommandHandler extends MessageHandler
{
    public function handle(UpdateCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $delivery = $em->getRepository(DeliveryDoc::class)->findOneBy(['number' => $command->id]);
        if (!$delivery instanceof DeliveryDoc) {
            throw new NotFoundHttpException(sprintf('Доставка %d не найдена', $command->id));
        }

        if ('completed' == $delivery->getStatusCode()) {
            throw new BadRequestHttpException(sprintf('Редактирование данных доставки не доступно для статуса Завершено', $command->id));
        }

        $delivery->setDate(new \DateTime($command->date));
        $delivery->setStatusCode($command->statusCode);
        $delivery->setCourierId($command->courierId);
    }
}