<?php

namespace AppBundle\Bus\Cart\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\Representative;
use AppBundle\Enum\DeliveryTypeCode;
use AppBundle\Enum\OrderType;
use AppBundle\Enum\RepresentativeTypeCode;
use AppBundle\Entity\PaymentType;

class GetSummaryQueryHandler extends MessageHandler
{
    public function handle(GetSummaryQuery $query)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $geoCity = $this->getGeoCity();
        $representative = $em->getRepository(Representative::class)->findOneBy(['geoPointId' => $query->geoPointId]);
        $paymentType = $em->getRepository(PaymentType::class)->findOneBy(['code' => $query->paymentTypeCode]);
        $products = $query->products;

        if (OrderType::RETAIL == $query->orderTypeCode) {
            foreach ($products as $product) {
                $product->price = $product->storePricetag ?? $product->price;
                $product->priceWithDiscount = $product->storePricetag ?? $product->price;
                $product->deliveryTax = 0;
            }
        }

        if (DeliveryTypeCode::COURIER !== $query->deliveryTypeCode || !$query->needLifting) {
            foreach ($products as $product) {
                $product->liftingCost = 0;
            }
        }

        if (DeliveryTypeCode::COURIER === $query->deliveryTypeCode) {
            $deliveryCharges = $representative->getDeliveryTax();

            if ($query->needLifting) {
                if ($query->hasLift) {
                    $floor = 2;
                } else {
                    $floor = $query->floor;
                }
            }
        }

        if (DeliveryTypeCode::TRANSPORT_COMPANY === $query->deliveryTypeCode) {
            $transportCompanyDeliveryCharges = $this->getParameter('const.delivery.transport_company.cost');
        }

        if (DeliveryTypeCode::POST === $query->deliveryTypeCode) {
            $postDeliveryCharges = $this->getParameter('const.delivery.post.cost');
        }

        if ($paymentType instanceof PaymentType) {
            $paymentTypeComissionPercent = $paymentType->getCashlessPercent();
            $paymentTypeName = $paymentType->getName();
            $paymentTypeCode = $paymentType->getCode();
        }

        if (in_array($query->deliveryTypeCode, [DeliveryTypeCode::COURIER, DeliveryTypeCode::EX_WORKS])) {
            if (RepresentativeTypeCode::PARTNER == $representative->getType() || 214 == $representative->getGeoPointId()) {
                foreach ($products as $product) {
                    $amount = $product->price * $product->quantity;
                    $product->regionDeliveryTax = round((1000000 < $amount ? 100000 : $amount * .1) / $product->quantity, -3);
                }
            } elseif (RepresentativeTypeCode::FRANCHISER == $representative->getType()) {
                $mostExpensive = reset($products);
                $mostExpensiveKey = key($products);

                foreach ($products as $key => $product) {
                    if ($mostExpensive->price < $product->price) {
                        $mostExpensive = $product;
                        $mostExpensiveKey = $key;
                    }

                    $product->regionDeliveryTax = $product->deliveryTax;
                }

                $products[$mostExpensiveKey]->regionDeliveryTax += $representative->getDeliveryTax();
            }
        }

        return new DTO\CartSummary($products, $query->discountCode, $query->discountCodeId, $deliveryCharges ?? 0, $floor ?? 0, $transportCompanyDeliveryCharges ?? 0, $postDeliveryCharges ?? 0, $paymentTypeComissionPercent ?? 0, $paymentTypeCode ?? '', $paymentTypeName ?? '');
    }
}
