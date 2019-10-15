<?php

namespace AppBundle\Bus\Order\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\OrderType;
use AppBundle\Enum\OrderTypeCode;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command)
    {
        $cart = $this->get('query_bus')->handle(new \AppBundle\Bus\Cart\Query\GetQuery(['discountCode' => $this->get('session')->get('discountCode')]));
        $items = [];

        foreach ($cart->products as $product) {
            $items[] = ['baseProductId' => $product->id, 'quantity' => $product->quantity];
        }

        $api = $this->getUserIsEmployee() ? $this->get('user.api.client') : $this->get('site.api.client');

        switch ($command->typeCode) {
            case OrderType::CONSUMABLES:
                $typeCode = OrderTypeCode::CONSUMABLES;
                break;
            case OrderType::EQUIPMENT:
                $typeCode = OrderTypeCode::EQUIPMENT;
                break;
            case OrderType::LEGAL:
                $typeCode = OrderTypeCode::LEGAL;
                break;
            case OrderType::NATURAL:
                $typeCode = OrderTypeCode::SITE;
                break;
            case OrderType::RESUPPLY:
                $typeCode = OrderTypeCode::RESUPPLY;
                break;
            case OrderType::RETAIL:
                $typeCode = OrderTypeCode::SHOP;
                break;
        }

        if (empty($typeCode)) {
            throw new BadRequestHttpException('Указан не существующий тип заказа');
        }

        $params = [
            'typeCode' => $typeCode,
            'client' => $command->client,
            'address' => $command->address,
            'passport' => $command->passport,
            'organizationDetails' => $command->organizationDetails,
            'geoCityId' => $command->geoCityId,
            'geoPointId' => $command->geoPointId,
            'paymentTypeCode' => $command->paymentTypeCode,
            'creditDownPayment' => $command->creditDownPayment,
            'deliveryTypeCode' => $command->deliveryTypeCode,
            'needLifting' => $command->needLifting,
            'transportCompanyId' => $command->transportCompanyId,
            'isNotificationNeeded' => $this->getUserIsEmployee() ? $command->isNotificationNeeded : true,
            'isMarketingSubscribed' => $command->isMarketingSubscribed,
            'isCallNeeded' => $this->getUserIsEmployee() ? false : $command->isCallNeeded,
            'callNeedComment' => $command->callNeedComment,
            'comment' => $command->comment,
            'discountCode' => $cart->discountCode,
            'items' => $items,
        ];

        try {
            $result = $api->post('/api/v1/orders/', [], $params);
            $command->id = $result['id'];
        } catch (BadRequestHttpException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }
}
