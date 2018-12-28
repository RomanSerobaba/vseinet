<?php

namespace AppBundle\Bus\Order\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\OrderType;
use AppBundle\Entity\GeoPoint;
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
                    $ourSellerCounteragentId,
                    $geoCityId,
                    $geoPointId,
                    'orderTypeCode' => $command->typeCode,
                    $financialCounteragentId,
                    $paymentTypeCode,
                    $discountCodeId,
                    $deliveryTypeCode,
                    $isCallNeeded,
                    $callNeedComment,
                    $items,
                    $isReserve
                ];
                break;

            case OrderType::NATURAL:
                $params =[
                    $ourSellerCounteragentId,
                    $geoCityId,
                    $geoPointId,
                    'orderTypeCode' => $command->typeCode,
                    $financialCounteragentId,
                    $paymentTypeCode,
                    $discountCodeId,
                    $deliveryTypeCode,
                    $isCallNeeded,
                    $callNeedComment,
                    $items,
                    $isReserve
                ];
                break;

            case OrderType::RETAIL:
                $params =[
                    $ourSellerCounteragentId,
                    $geoCityId,
                    $geoPointId,
                    'orderTypeCode' => $command->typeCode,
                    $financialCounteragentId,
                    $paymentTypeCode,
                    $discountCodeId,
                    $deliveryTypeCode,
                    $isCallNeeded,
                    $callNeedComment,
                    $items,
                    $isReserve
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
