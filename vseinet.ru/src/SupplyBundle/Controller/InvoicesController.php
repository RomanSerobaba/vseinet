<?php

namespace SupplyBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use SupplyBundle\Bus\Invoices\Query;
use SupplyBundle\Bus\Invoices\Command;

/**
 * @VIA\Section("Счета")
 */
class InvoicesController extends RestController
{
    /**
     * @VIA\Post(
     *     path="/supplies/{id}/separate/",
     *     description="Создание отдельного счета на представительство",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="SupplyBundle\Bus\Invoices\Command\CreateSeparateSupplierInvoiceCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201,
     *     properties={
     *         @VIA\Property(name="id", type="integer")
     *     }
     * )
     */
    public function addSupplierInvoiceToPointAction(int $id, Request $request)
    {
        $uuid = $this->get('uuid.manager')->generate();
        $this->get('command_bus')->handle(new Command\CreateSeparateSupplierInvoiceCommand($request->request->all(), ['id' => $id, 'uuid' => $uuid,]));

        return [
            'id' => $this->get('uuid.manager')->loadId($uuid),
        ];
    }

    /**
     * @VIA\Get(
     *     path="/points/forSupply/",
     *     description="Получение списка точек для создания отдельного счета",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Invoices\Query\GetSupplyPointsQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="SupplyBundle\Bus\Invoices\Query\DTO\SupplyPoints")
     *     }
     * )
     */
    public function getSupplyPointsAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetSupplyPointsQuery($request->query->all()), $points);

        return $points;
    }

    /**
     * @VIA\Get(
     *     path="/supplierInvoices/{id}/candidates/",
     *     description="Получение списка позиций, которые можно добавить в счет",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Invoices\Query\GetSupplierInvoiceListQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array")
     *     }
     * )
     */
    public function getSupplierInvoiceListQueryAction(int $id, Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetSupplierInvoiceListQuery($request->request->all(), ['id' => $id,]), $list);

        return $list;
    }

    /**
     * @VIA\Delete(
     *     path="/supplyItems/",
     *     description="Удалить позиции из счета",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Invoices\Command\DeletePositionFromSupplierInvoiceCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function deletePositionFromSupplierInvoiceAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\DeletePositionFromSupplierInvoiceCommand($request->request->all()));
    }

    /**
     * @VIA\Put(
     *     path="/supplies/{id}/itemPrice/",
     *     description="Редактирование закупки позиции",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="SupplyBundle\Bus\Invoices\Command\EditSupplierInvoiceProductPriceCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201,
     *     properties={
     *         @VIA\Property(name="id", type="integer")
     *     }
     * )
     */
    public function editItemPriceAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\EditSupplierInvoiceProductPriceCommand($request->request->all(), ['id' => $id,]));
    }

    /**
     * @VIA\Put(
     *     path="/supplies/{id}/productPrice/",
     *     description="Редактирование закупки товара",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="SupplyBundle\Bus\Invoices\Command\EditSupplierInvoiceItemPriceCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201,
     *     properties={
     *         @VIA\Property(name="id", type="integer")
     *     }
     * )
     */
    public function editProductPriceAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\EditSupplierInvoiceItemPriceCommand($request->request->all(), ['id' => $id,]));
    }

    /**
     * @VIA\Put(
     *     path="/supplies/{id}/arrivingTime/",
     *     description="Изменить дату прихода",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Invoices\Command\UpdateArrivingTimeCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
//    public function updateArrivingTimeAction(int $id, Request $request)
//    {
//        $this->get('command_bus')->handle(new Command\UpdateArrivingTimeCommand($request->request->all(), ['id' => $id,]));
//    }
}