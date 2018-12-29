<?php

namespace AppBundle\Bus\Cart\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\DiscountCode;
use AppBundle\Entity\Representative;
use AppBundle\Enum\DeliveryTypeCode;
use AppBundle\Enum\PaymentTypeCode;
use AppBundle\Enum\GoodsConditionCode;
use AppBundle\Entity\TransportCompany;
use AppBundle\Entity\PaymentType;

class GetQueryHandler extends MessageHandler
{
    public function handle(GetQuery $query)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $geoCity = $this->getGeoCity();
        $discount = $em->getRepository(DiscountCode::class)->findOneBy(['code' => $query->discountCode]);
        $representative = $em->getRepository(Representative::class)->findOneBy(['geoPointId' => $query->geoPointId]);
        $transportCompany = $em->getRepository(TransportCompany::class)->findOneBy(['id' => $query->transportCompanyId]);
        $paymentType = $em->getRepository(PaymentType::class)->findOneBy(['code' => $query->paymentTypeCode]);
        $spec = "";
        $params = [];

        if ($representative instanceof Representative) {
            $spec .= ",
                (
                    SELECT
                        SUM(grrc.delta)
                    FROM AppBundle:GoodsReserveRegisterCurrent AS grrc
                    JOIN AppBundle:GeoRoom AS gr WITH gr.id = grrc.geoRoomId
                    WHERE grrc.baseProductId = bp.id AND gr.geoPointId = :geoPointId AND grrc.conditionCode = :goodsConditionCode_FREE AND grrc.goodsPalleteId IS NULL AND grrc.orderItemId IS NULL
                ),
                (
                    SELECT
                        pp.price
                    FROM AppBundle:ProductPricetag AS pp
                    WHERE pp.baseProductId = bp.id AND pp.geoPointId = :geoPointId
                )";
            $params['geoPointId'] = $representative->getGeoPointId();
            $params['goodsConditionCode_FREE'] = GoodsConditionCode::FREE;
        } else {
            $spec .= ", 0, 0";
        }

        if (DeliveryTypeCode::COURIER === $query->deliveryTypeCode && $query->needLifting) {
            $spec .= ", p.liftingTax";
        } else {
            $spec .= ", 0";
        }

        if ($discount instanceof DiscountCode) {
            $spec .= ", p.discountAmount";
            $this->get('session')->set('discountCode', $query->discountCode);
        } else {
            $spec .= ", 0";
        }

        if (null !== $user) {
            $q = $em->createQuery("
                SELECT
                    NEW AppBundle\Bus\Cart\Query\DTO\Product (
                        bp.id,
                        bp.name,
                        bp.categoryId,
                        bp.minQuantity,
                        bpi.basename,
                        p.price,
                        p.productAvailabilityCode,
                        p.deliveryTax,
                        c.quantity
                        {$spec}
                    )
                FROM AppBundle:Cart c
                INNER JOIN AppBundle:BaseProduct AS bp WITH bp.id = c.baseProductId
                LEFT OUTER JOIN AppBundle:BaseProductImage AS bpi WITH bpi.baseProductId = bp.id AND bpi.sortOrder = 1
                INNER JOIN AppBundle:Product AS p WITH p.baseProductId = bp.id
                WHERE c.userId = :userId AND p.geoCityId = :geoCityId
            ");
            $q->setParameters([
                'userId' => $user->getId(),
                'geoCityId' => $geoCity->getId(),
            ] + $params);
            $products = $q->getResult('IndexByHydrator');
        }
        else {
            $products = $this->get('session')->get('cart', []);

            if (!empty($products)) {
                $q = $em->createQuery("
                    SELECT
                        NEW AppBundle\Bus\Cart\Query\DTO\Product (
                            bp.id,
                            bp.name,
                            bp.categoryId,
                            bp.minQuantity,
                            bpi.basename,
                            p.price,
                            p.productAvailabilityCode,
                            p.deliveryTax,
                            0
                            {$spec}
                        )
                    FROM AppBundle:BaseProduct AS bp
                    LEFT OUTER JOIN AppBundle:BaseProductImage AS bpi WITH bpi.baseProductId = bp.id AND bpi.sortOrder = 1
                    INNER JOIN AppBundle:Product AS p WITH p.baseProductId = bp.id
                    WHERE bp.id IN (:ids) AND p.geoCityId = :geoCityId
                ");
                $q->setParameters([
                    'ids' => array_keys($products),
                    'geoCityId' => $geoCity->getId(),
                ] + $params);
                foreach ($q->getResult() as $product) {
                    $product->quantity = intval($products[$product->id]['quantity']);
                    $products[$product->id] = $product;
                }
            }
        }

        if (DeliveryTypeCode::COURIER === $query->deliveryTypeCode) {
            $deliveryCharges = $representative->getDeliveryTax();

            if ($query->needLifting) {
                if ($query->hasLift) {
                    $query->floor = 1;
                }

                $liftingCharges = 0;

                foreach ($products as $product) {
                    $liftingCharges += $product->quantity * $query->floor * $query->liftingCost;
                }
            }
        }

        if (DeliveryTypeCode::TRANSPORT_COMPANY === $query->deliveryTypeCode) {
            if ($transportCompany instanceof TransportCompany) {
                $transportCompanyDeliveryCharges = $transportCompany->getTax();
            }
        }

        if ($paymentType instanceof PaymentType) {
            $paymentTypeComissionPercent = $paymentType->getCashlessPercent();
        }

        return new DTO\Cart($products, $deliveryCharges ?? 0, $liftingCharges ?? 0, $paymentTypeComissionPercent ?? 0, $transportCompanyDeliveryCharges ?? 0, $discount instanceof DiscountCode ? $discount->getCode() : null);
    }
}
