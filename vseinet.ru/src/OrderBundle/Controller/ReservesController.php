<?php

namespace OrderBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use OrderBundle\Bus\Reserves\Query;
use OrderBundle\Bus\Reserves\Command;

/**
 * @VIA\Section("Товарные остатки заказа")
 */
class ReservesController extends RestController
{
    /**
     * @VIA\Get(
     *     path="/points/forOrderReservation/",
     *     description="Отображение списка точек для резервирования",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="OrderBundle\Bus\Reserves\Query\GetReservePointsQuery"),
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="OrderBundle\Bus\Reserves\Query\DTO\ReservePointsQuery")
     *     }
     * )
     */
    public function getAvailableVariantsAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetReservePointsQuery($request->query->all()), $points);

        return $points;
    }

    /**
     * @VIA\Put(
     *     path="/orderItems/{id}/reserveFromPoint/",
     *     description="Подтверждение резерва с наличия",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="OrderBundle\Bus\Reserves\Command\ReserveConfirmationCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function reserveConfirmationAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\ReserveConfirmationCommand($request->request->all(), ['id' => $id,]));
    }

    /**
     * @VIA\Get(
     *     path="/orderItems/{id}/reserveFromPoint",
     *     description="Подтверждение резерва с наличия (GET)",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="OrderBundle\Bus\Reserves\Query\GetReserveConfirmationQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(name="processing", type="integer"),
     *         @VIA\Property(name="reserved", type="integer")
     *     }
     * )
     */
    public function getReserveConfirmationAction(int $id, Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetReserveConfirmationQuery($request->query->all(), ['id' => $id,]), $result);

        return $result;
    }
}