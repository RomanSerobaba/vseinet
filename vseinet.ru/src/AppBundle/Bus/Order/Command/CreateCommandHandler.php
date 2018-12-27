<?php 

namespace AppBundle\Bus\Order\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\OrderType;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command)
    {
        $api = $this->get('user.api.client');

        switch ($command->typeCode) {
            case OrderType::CONSUMABLES:
            case OrderType::EQUIPMENT:
            case OrderType::RESUPPLY:
                $params =[
                    $ourSellerCounteragentId,
                    $geoCityId,
                    $geoPointId,
                    'orderTypeCode' => $command->typeCode,
                    $items
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
            $id = $api->post('/api/v1/orders/', $params);
        } catch (BadRequestHttpException $e) {
            return null;
        }

        $command->id = $id;
    }
}
