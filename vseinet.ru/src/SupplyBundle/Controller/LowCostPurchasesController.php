<?php

namespace SupplyBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use SupplyBundle\Bus\LowCostPurchases\Query;

/**
 * @VIA\Section("Акции и распродажи поставщиков")
 */
class LowCostPurchasesController extends RestController
{
    /**
     * @VIA\Get(
     *     path="/categories/forLowCostPurchases/",
     *     description="Категории на странице c общим счетчиком товаров и по каждой подкатегорией",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\LowCostPurchases\Query\GetCategoriesQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", name="list", model="SupplyBundle\Bus\LowCostPurchases\Query\DTO\Categories"),
     *         @VIA\Property(type="integer", name="totalCount")
     *     }
     * )
     */
    public function getCategoriesAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetCategoriesQuery(), $list);

        return $list;
    }

    /**
     * @VIA\Get(
     *     path="/products/forLowCostPurchases/",
     *     description="Получение списка товаров по подкатегории",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\LowCostPurchases\Query\GetProductsQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="SupplyBundle\Bus\LowCostPurchases\Query\DTO\Products")
     *     }
     * )
     */
    public function getProductsAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetProductsQuery($request->query->all()), $list);

        return $list;
    }
}