<?php

namespace AppBundle\Bus\Order\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\OrderType;
use AppBundle\Entity\GeoPoint;
use AppBundle\Entity\DiscountCode;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command)
    {
        $em = $this->getDoctrine()->getManager();
        $this->get('query_bus')->handle(new \AppBundle\Bus\Cart\Query\GetQuery(), $cart);
        $items = [];

        foreach ($cart->products as $product) {
            $items[] = ['baseProductId' => $product->id, 'quantity' => $product->quantity];
        }

        $user = $this->security->getToken()->getUser();

        if (count($user->geoRooms) > 0) {
            $points = array_column($user->geoRooms, 'geo_point_id');
        } else {
            $points = [$this->getParameter('default.point.id')];
        }

        $q = $this->em->createQuery("
            SELECT
                NEW AppBundle\Bus\Order\Query\DTO\GeoPoint (
                    p.id,
                    p.name,
                    a.address,
                    r.hasRetail,
                    r.hasDelivery,
                    r.hasRising,
                    p.geoCityId
                )
            FROM AppBundle:GeoPoint AS p
            JOIN AppBundle:Representative AS r WITH r.geoPointId = p.id
            LEFT JOIN AppBundle:GeoAddress AS a WITH a.id = p.geoAddressId
            WHERE p.id IN (:ids) AND r.isActive = TRUE
        ");
        $q->setParameter('ids', $points);
        $point = $q->getSingleResult();

        $discountCode = $em->getRepository(DiscountCode::class)->findOneBy(['code' => $cart->discountCode]);

        $api = $this->get('user.api.client');

        switch ($command->typeCode) {
            case OrderType::CONSUMABLES:
            case OrderType::EQUIPMENT:
            case OrderType::RESUPPLY:
                if (!$command->geoPointId) {
                    throw new BadRequestHttpException('Точка не указана');
                }

                $geoPoint = $em->getRepository(GeoPoint::class)->find($command->geoPointId);

                if (!$geoPoint instanceof GeoPoint) {
                    throw new NotFoundHttpException(sprintf('Точка с ид %d не найдена', $command->geoPointId));
                }

                $params =[
                    'ourSellerCounteragentId' => $this->getParameter('default.ourConteragent.id'),
                    'geoCityId' => $geoPoint->getGeoCityId(),
                    'geoPointId' => $command->geoPointId,
                    'orderTypeCode' => $command->typeCode,
                    'items' => $items,
                ];
                break;

            case OrderType::LEGAL:
                $params =[
                    'ourSellerCounteragentId' => $this->getParameter('default.ourConteragent.id'),
                    'geoCityId' => $command->geoCityId,
                    'geoPointId' => $command->geoPointId ?? $this->getParameter('default.point.id'),
                    'orderTypeCode' => $command->typeCode,
                    'financialCounteragentId' => $financialCounteragentId,
                    'paymentTypeCode' => $command->paymentTypeCode,
                    'discountCodeId' => $discountCode->id,
                    'deliveryTypeCode' => $command->deliveryTypeCode,
                    'isCallNeeded' => $command->isCallNeeded,
                    'callNeedComment' => $command->callNeedComment,
                    'items' => $items,
                ];
                break;

            case OrderType::NATURAL:
                $params =[
                    'ourSellerCounteragentId' => $this->getParameter('default.ourConteragent.id'),
                    'geoCityId' => $command->geoCityId,
                    'geoPointId' => $command->geoPointId ?? $this->getParameter('default.point.id'),
                    'orderTypeCode' => $command->typeCode,
                    'financialCounteragentId' => $financialCounteragentId,
                    'paymentTypeCode' => $command->paymentTypeCode,
                    'discountCodeId' => $discountCode->id,
                    'deliveryTypeCode' => $command->deliveryTypeCode,
                    'isCallNeeded' => $command->isCallNeeded,
                    'callNeedComment' => $command->callNeedComment,
                    'items' => $items,
                ];
                break;

            case OrderType::RETAIL:
                $params =[
                    'ourSellerCounteragentId' => $this->getParameter('default.ourConteragent.id'),
                    'geoCityId' => $point->geoCityId,
                    'geoPointId' => $point->id,
                    'orderTypeCode' => $command->typeCode,
                    'financialCounteragentId' => $financialCounteragentId,
                    'paymentTypeCode' => $command->paymentTypeCode,
                    'discountCodeId' => $discountCode->id,
                    'deliveryTypeCode' => $command->deliveryTypeCode,
                    'isCallNeeded' => $command->isCallNeeded,
                    'callNeedComment' => $command->callNeedComment,
                    'items' => $items,
                    'isReserve' => true
                ];
                break;
        }
        try {
            $id = $api->post('/api/v1/orders/', [], $params);
        } catch (BadRequestHttpException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $command->id = $id;
    }
}
