<?php

namespace AppBundle\Bus\Order\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\OrderType;
use AppBundle\Enum\OrderTypeCode;
use AppBundle\Entity\GeoPoint;
use AppBundle\Entity\DiscountCode;
use AppBundle\Entity\FinancialCounteragent;
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

        $user = $this->getUser();

        $discountCode = $em->getRepository(DiscountCode::class)->findOneBy(['code' => $cart->discountCode]);
        $discountCodeId = $discountCode instanceof DiscountCode ? $discountCode->getId() : null;

        $api = NULL !== $user ? $this->get('user.api.client') : $this->get('site.api.client');

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
                    'cityId' => $geoPoint->getGeoCityId(),
                    'representativeId' => $command->geoPointId,
                    'orderTypeCode' => $command->typeCode,
                    'items' => $items,
                ];
                break;

            case OrderType::LEGAL:
                $params =[
                    'ourSellerCounteragentId' => $command->organizationDetails->withVat ? $this->getParameter('default.counteragentWithVat.id') : $this->getParameter('default.counteragentWithoutVat.id'),
                    'cityId' => $command->geoCityId,
                    'representativeId' => $command->geoPointId ?? $this->getParameter('default.point.id'),
                    'orderTypeCode' => $command->typeCode,
                    'person' => $command->userData,
                    'paymentTypeCode' => $command->paymentTypeCode,
                    'discountCodeId' => $discountCodeId,
                    'deliveryTypeCode' => $command->deliveryTypeCode,
                    'isCallNeeded' => $command->isCallNeeded ?? false,
                    'callNeedComment' => $command->callNeedComment,
                    'phone' => $command->userData->phone,
                    'additionalPhone' => $command->userData->additionalPhone,
                    'email' => $command->userData->email,
                    'transportCompanyId' => $command->transportCompanyId,
                    'creditDownPayment' => $command->creditDownPayment,
                    'isNotificationNeeded' => $command->isNotificationNeeded,
                    'transportCompanyId' => $command->transportCompanyId,
                    'isMarketingSubscribed' => $command->isMarketingSubscribed,
                    'isTranscationalSubscribed' => $command->isTranscationalSubscribed,
                    'organizationDetails' => [
                        'name' => $command->organizationDetails->name,
                        'legalAddress' => $command->organizationDetails->legalAddress,
                        'settlementAccount' => $command->organizationDetails->settlementAccount,
                        'tin' => $command->organizationDetails->tin,
                        'kpp' => $command->organizationDetails->kpp,
                        'bankId' => $command->organizationDetails->bankId,
                    ],
                    'address' => [
                        'geoStreetId' => $command->geoAddress->geoStreetId,
                        'geoStreetName' => $command->geoAddress->geoStreetName,
                        'house' => $command->geoAddress->house,
                        'building' => $command->geoAddress->building,
                        'apartment' => $command->geoAddress->apartment,
                        'floor' => $command->geoAddress->floor,
                        'hasLift' => $command->geoAddress->hasLift,
                        'office' => $command->geoAddress->office,
                        'postalCode' => $command->geoAddress->postalCode,
                    ],
                    'items' => $items,
                ];
                break;

            case OrderType::NATURAL:
                $params =[
                    'ourSellerCounteragentId' => $this->getParameter('default.ourConteragent.id'),
                    'cityId' => $command->geoCityId,
                    'representativeId' => $command->geoPointId ?? $this->getParameter('default.point.id'),
                    'orderTypeCode' => OrderTypeCode::SITE,
                    'person' => $command->userData,
                    'paymentTypeCode' => $command->paymentTypeCode,
                    'discountCodeId' => $discountCodeId,
                    'deliveryTypeCode' => $command->deliveryTypeCode,
                    'isCallNeeded' => $command->isCallNeeded ?? false,
                    'callNeedComment' => $command->callNeedComment,
                    'transportCompanyId' => $command->transportCompanyId,
                    'address' => [
                        'geoStreetId' => $command->geoAddress->geoStreetId,
                        'geoStreetName' => $command->geoAddress->geoStreetName,
                        'house' => $command->geoAddress->house,
                        'building' => $command->geoAddress->building,
                        'apartment' => $command->geoAddress->apartment,
                        'floor' => $command->geoAddress->floor,
                        'hasLift' => $command->geoAddress->hasLift,
                        'office' => $command->geoAddress->office,
                        'postalCode' => $command->geoAddress->postalCode,
                    ],
                    'items' => $items,
                ];
                break;

            case OrderType::RETAIL:
                if (NULL !== $user && count($user->geoRooms) > 0) {
                    $points = array_column($user->geoRooms, 'geo_point_id');
                } else {
                    $points = [$this->getParameter('default.point.id')];
                }

                $q = $em->createQuery("
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
                $point = $q->getOneOrNullResult();

                if (empty($point)) {
                    throw new BadRequestHttpException('Невозможно провести продажу с магазина, так как точка, к котрой вы привязаны не является розничной или была деактивирована.');
                }

                $params =[
                    'ourSellerCounteragentId' => $this->getParameter('default.ourConteragent.id'),
                    'cityId' => $point->geoCityId,
                    'representativeId' => $point->id,
                    'orderTypeCode' => OrderTypeCode::SHOP,
                    'person' => $command->userData,
                    'financialCounteragentId' => $financialCounteragentId,
                    'paymentTypeCode' => $command->paymentTypeCode,
                    'discountCodeId' => $discountCode->id,
                    'deliveryTypeCode' => $command->deliveryTypeCode,
                    'isCallNeeded' => $command->isCallNeeded ?? false,
                    'callNeedComment' => $command->callNeedComment,
                    'isReserve' => true,
                    'transportCompanyId' => $command->transportCompanyId,
                    'address' => [
                        'geoStreetId' => $command->geoAddress->geoStreetId,
                        'house' => $command->geoAddress->house,
                        'building' => $command->geoAddress->building,
                        'apartment' => $command->geoAddress->apartment,
                        'floor' => $command->geoAddress->floor,
                        'hasLift' => $command->geoAddress->hasLift,
                        'office' => $command->geoAddress->office,
                        'postalCode' => $command->geoAddress->postalCode,
                    ],
                    'items' => $items,
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
