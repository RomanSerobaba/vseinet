<?php

namespace AppBundle\Bus\Cart\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\DiscountCode;
use AppBundle\Entity\Representative;
use AppBundle\Enum\DeliveryTypeCode;
use AppBundle\Enum\PaymentTypeCode;
use AppBundle\Enum\OrderType;
use AppBundle\Enum\GoodsConditionCode;
use AppBundle\Entity\TransportCompany;
use AppBundle\Entity\PaymentType;

class GetSummaryQueryHandler extends MessageHandler
{
    public function handle(GetSummaryQuery $query)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $geoCity = $this->getGeoCity();
        $representative = $em->getRepository(Representative::class)->findOneBy(['geoPointId' => $query->geoPointId]);
        $transportCompany = $em->getRepository(TransportCompany::class)->findOneBy(['id' => $query->transportCompanyId]);
        $paymentType = $em->getRepository(PaymentType::class)->findOneBy(['code' => $query->paymentTypeCode]);
        $products = $query->cart->products;

        if (OrderType::RETAIL == $query->orderTypeCode) {
            foreach ($products as $key => $product) {
                $products[$key]->price = $product->storePricetag ?? $product->price;
            }
        }

        if (DeliveryTypeCode::COURIER !== $query->deliveryTypeCode || !$query->needLifting) {
            foreach ($products as $key => $product) {
                $products[$key]->liftingCost = 0;
            }
        }

        if (DeliveryTypeCode::COURIER === $query->deliveryTypeCode) {
            $deliveryCharges = $representative->getDeliveryTax();

            if ($query->needLifting) {
                if ($query->hasLift) {
                    $floor = 1;
                } else {
                    $floor = $query->floor;
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
            $paymentTypeName = $paymentType->getName();
        }

        return new DTO\CartSummary($products, $query->cart->discountCode, $deliveryCharges ?? 0, $floor ?? 0, $transportCompanyDeliveryCharges ?? 0, $deliveryToRepresentativeTaxAmount ?? 0, $paymentTypeComissionPercent ?? 0, $paymentTypeName);
    }
}
