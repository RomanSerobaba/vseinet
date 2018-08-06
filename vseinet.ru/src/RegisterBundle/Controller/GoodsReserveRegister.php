<?php

namespace RegisterBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use RegisterBundle\Bus\GoodsReserveRegister\Query;
//use RegisterBundle\Bus\GoodsReserveRegister\Command;

/**
 * @VIA\Section("Регистр остатков")
 */
class GoodsReserveRegister extends RestController
{
    //ToDo add turnovers, details!
    //ToDo need query?
    
    /**
     * @VIA\Get(
     *     path="/registers/reserve/remnants/",
     *     description="Получить остатки товаров. Для отладки выборка ограничена.",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="RegisterBundle\Bus\GoodsReserveRegister\Query\RemnantsQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="RegisterBundle\Bus\GoodsReserveRegister\Query\DTO\RemnantsList")
     *     }
     * )
     */
    public function listAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\RemnantsQuery($request->query->all()), $items);

        return $items;
    }
    
    /**
     * @VIA\Get(
     *     path="/goodsReservesRegister/",
     *     description="Получить операции по регистру товарных остатков.",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="RegisterBundle\Bus\GoodsReserveRegister\Query\HistoryQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="RegisterBundle\Bus\GoodsReserveRegister\Query\DTO\Item")
     *     }
     * )
     */
    public function historyAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\HistoryQuery($request->query->all()), $items);

        return $items;
    }

}
