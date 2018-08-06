<?php

namespace PricingBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use PricingBundle\Bus\Product\Query;
use PricingBundle\Bus\Product\Command;

/**
 * @VIA\Section("Товар для определенного города")
 */
class ProductController extends RestController
{
    /**
     * @VIA\Get(
     *     path="/products/byCode/{code}/",
     *     description="Получить товар по коду",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="PricingBundle\Bus\Product\Query\GetListQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="PricingBundle\Bus\Product\Query\DTO\Product")
     *     }
     * )
     */
    public function getByCodeAction(int $code)
    {
        $this->get('query_bus')->handle(new Query\GetListQuery(['baseProductId' => $code, 'cityId' => $this->get('user.identity')->getUser()->getCityId()]), $items);

        if (0 == count($items)) {
            $this->get('query_bus')->handle(new Query\GetListQuery(['baseProductId' => $code, 'cityId' => null]), $items);
        }
        
        return empty($items) ? null : reset($items);
    }
}