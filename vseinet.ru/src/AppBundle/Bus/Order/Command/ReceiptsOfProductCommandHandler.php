<?php

namespace AppBundle\Bus\Order\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Enum\OrderTypeCode;
use AppBundle\Enum\PaymentTypeCode;
use AppBundle\Enum\DeliveryTypeCode;
use AppBundle\Entity\BaseProduct;
use Doctrine\ORM\Query\ResultSetMapping;

class ReceiptsOfProductCommandHandler extends MessageHandler
{
    public function handle(ReceiptsOfProductCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $baseProduct = $em->getRepository(BaseProduct::class)->find($command->baseProductId);
        if (!$baseProduct instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар с кодом %d  не найден', $command->baseProductId));
        }

        $geoCity = $this->getGeoCity();
        $representative = $this->get('representative.identity')->getRepresentative();
        $address = new Schema\Address(['geoCity' => $geoCity->getId()]);

        $params = [
            'typeCode' => OrderTypeCode::SITE,
            'client' => $command->userData,
            'geoCityId' => $address->geoCityId,
            'geoPointId' => $representative->getGeoPointId(),
            'address' => $address,
            'paymentTypeCode' => PaymentTypeCode::CASH,
            'deliveryTypeCode' => DeliveryTypeCode::EX_WORKS,
            'isCallNeeded' => false,
            'items' => [
                ['baseProductId' => $baseProduct->getId(), 'quantity' => 1],
            ],
        ];

        $result = $this->get('site.api.client')->post('/api/v1/orders/', [], $params);

        $q = $em->createNativeQuery('
            INSERT INTO client_order_item_tracking (order_item_id, expires_at)
            SELECT oi.id, :expires_at
            FROM order_item AS oi
            WHERE oi.order_did = :id
        ', new ResultSetMapping());
        $q->setParameter('id', $result['id']);
        $q->setParameter('expires_at', new \DateTime(sprintf('+%d days', $command->trackingPeriod)));
        $q->execute();

        return $result['id'];
    }
}
