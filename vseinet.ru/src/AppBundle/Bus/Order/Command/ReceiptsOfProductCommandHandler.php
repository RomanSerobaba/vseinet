<?php

namespace AppBundle\Bus\Order\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Enum\OrderTypeCode;
use AppBundle\Enum\PaymentTypeCode;
use AppBundle\Enum\DeliveryTypeCode;
use AppBundle\Entity\BaseProduct;

class ReceiptsOfProductCommandHandler extends MessageHandler
{
    public function handle(ReceiptsOfProductCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $baseProduct = $em->getRepository(BaseProduct::class)->find($command->baseProductId);
        if (!$baseProduct instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар с кодом %d  не найден', $command->baseProductId));
        }

        $address = new Schema\Address();
        $address->geoCityId = 1;

        $params = [
            'typeCode' => OrderTypeCode::SITE,
            'client' => $command->userData,
            'geoCityId' => $address->geoCityId,
            'geoPointId' => 141,
            'address' => $address,
            'paymentTypeCode' => PaymentTypeCode::CASH,
            'deliveryTypeCode' => DeliveryTypeCode::EX_WORKS,
            'isCallNeeded' => false,
            'items' => [
                ['baseProductId' => $baseProduct->getId(), 'quantity' => 1],
            ],
        ];

        $result = $this->get('site.api.client')->post('/api/v1/orders/', [], $params);

        return $result['id'];
    }
}
