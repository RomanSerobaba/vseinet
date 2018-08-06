<?php

namespace ReservesBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use ReservesBundle\Bus\InventoryProduct\Query;
use ReservesBundle\Bus\InventoryProduct\Command;

/**
 * @VIA\Section("Инвентаризация - список товара")
 */
class InventoryProductController extends RestController
{

    /**
     * @VIA\Get(
     *     path="/inventoriesProducts/",
     *     description="Получить список товара инвентаризации",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\InventoryProduct\Query\GetQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ReservesBundle\Bus\InventoryProduct\Query\DTO\InventoryProducts")
     *     }
     * )
     */
    public function GetAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetQuery($request->query->all()), $items);

        return $items;
    }

    /**
     * @VIA\Post(
     *     path="/inventoriesProducts/",
     *     description="Заполнить/Перезаполнить список товара в инвентаризации",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\InventoryProduct\Command\ResetCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function ResetAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\ResetCommand($request->request->all()));
    }

}
