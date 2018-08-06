<?php

namespace SupplyBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use SupplyBundle\Bus\Suppliers\Query;
use SupplyBundle\Bus\Suppliers\Command;

/**
 * @VIA\Description("Поставщики")
 * @VIA\Section("Поставщики")
 */
class SuppliersController extends RestController
{
    /**
     * @VIA\Get(
     *     path="/suppliers/forOrdersShipping/",
     *     description="Получение списка поставщиков со счетчиками",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Suppliers\Query\GetShippingQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="SupplyBundle\Bus\Suppliers\Query\DTO\Suppliers")
     *     }
     * )
     */
    public function suppliersAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetShippingQuery($request->query->all()), $suppliers);

        return $suppliers;
    }

    /**
     * @VIA\Post(
     *     path="/suppliers/{id}/shipping/",
     *     description="Начать отгрузку",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Suppliers\Command\StartShippingCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function startShippingAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\StartShippingCommand($request->request->all(), ['id' => $id,]));
    }

    /**
     * @VIA\Post(
     *     path="/suppliers/{id}/shippingClosing/",
     *     description="Завершить отгрузку",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Suppliers\Command\CloseShippingCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function endAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\CloseShippingCommand($request->request->all(), ['id' => $id,]));
    }

    /**
     * @VIA\Get(
     *     path="/suppliers/forSelect/",
     *     description="Получить список поставщиков для селекта",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Suppliers\Query\GetForSelectQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="SupplyBundle\Bus\Suppliers\Query\DTO\SuppliersForSelect")
     *     }
     * )
     */
    public function getForSelectAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetForSelectQuery(), $suppliers);

        return $suppliers;
    }












    /**
     * @VIA\Get(
     *     path="/suppliers/matrix/",
     *     description="Таблица матрица поставщиков и менеджеров",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Suppliers\Query\GetMatrixQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", name="managers"),
     *         @VIA\Property(type="array", name="suppliers"),
     *         @VIA\Property(type="array", name="suppliersInactive")
     *     }
     * )
     */
    public function martixAction()
    {
        $this->get('query_bus')->handle(new Query\GetMatrixQuery(), $matrix);

        return $matrix;
    }

    /**
     * @VIA\Patch(
     *     path="/suppliers/{id}/assignSupplier/",
     *     description="Назначить поставщика менеджеру",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Suppliers\Command\AssignSupplierCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function assignSupplierAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\AssignSupplierCommand($request->request->all(), ['id' => $id,]));
    }

    /**
     * @VIA\Patch(
     *     path="/suppliers/{id}/setMainCounteragent/",
     *     description="Назначить главноое юр. лицо",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Suppliers\Command\SetMainCounteragentCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function setMainCounteragentAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetMainCounteragentCommand($request->request->all(), ['id' => $id,]));
    }

    /**
     * @VIA\Patch(
     *     path="/suppliers/{id}/unlinkCounteragent/",
     *     description="Отвязать юр. лицо",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Suppliers\Command\UnlinkCounteragentCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function unlinkCounteragentAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\UnlinkCounteragentCommand($request->request->all(), ['id' => $id,]));
    }

    /**
     * @VIA\Get(
     *     path="/suppliers/{id}/counteragent/{cid}/",
     *     description="Получение информации о поставщике",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Suppliers\Query\GetCounteragentQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", name="managers"),
     *         @VIA\Property(type="array", name="suppliers"),
     *         @VIA\Property(type="array", name="suppliersInactive")
     *     }
     * )
     */
    public function getCounteragentAction(int $id, int $cid)
    {
        $this->get('query_bus')->handle(new Query\GetCounteragentQuery(['id' => $id, 'cid' => $cid,]), $info);

        return $info;
    }

    /**
     * @VIA\Patch(
     *     path="/suppliers/{id}/contract/",
     *     description="Проставить/убрать наличие договора с датой его окончания",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Suppliers\Command\ContactCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function contractAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\ContactCommand($request->request->all(), ['id' => $id,]));
    }

    /**
     * @VIA\Patch(
     *     path="/suppliers/{id}/counteragent/{cid}/edit/",
     *     description="Сохранение юр. лица",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Suppliers\Command\EditCounteragentCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function editCounteragenAction(int $id, int $cid, Request $request)
    {
        $this->get('command_bus')->handle(new Command\EditCounteragentCommand($request->request->all(), ['id' => $id, 'cid' => $cid,]));
    }

    /**
     * @VIA\Post(
     *     path="/suppliers/{id}/counteragent/new/",
     *     description="Создание юр. лица",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Suppliers\Command\NewCounteragentCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function newCounteragenAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\NewCounteragentCommand($request->request->all(), ['id' => $id,]));
    }

    /**
     * @VIA\Patch(
     *     path="/suppliers/{id}/edit/",
     *     description="Сохранение поставщика",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Suppliers\Command\SaveSupplierCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function editSupplierAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SaveSupplierCommand($request->request->all(), ['id' => $id,]));
    }

    /**
     * @VIA\Get(
     *     path="/suppliers/forOrdersProcessing/",
     *     description="Получение списка поставщиков со счетчиком заказов",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Suppliers\Query\GetProcessingQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="SupplyBundle\Bus\Suppliers\Query\DTO\SuppliersForOrdersProcessing")
     *     }
     * )
     */
    public function getProcessingAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetProcessingQuery($request->query->all()), $suppliers);

        return $suppliers;
    }

    /**
     * @VIA\Get(
     *     path="/suppliers/for1C/",
     *     description="Получение поставщиков",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Suppliers\Query\GetFor1CQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="SupplyBundle\Bus\Suppliers\Query\DTO\OneC")
     *     }
     * )
     */
    public function getFor1CAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetFor1CQuery($request->query->all()), $list);

        return $list;
    }

    /**
     * @VIA\Get(
     *     path="/suppliers/{id}/counteragents/",
     *     description="Получение контрагентов поставщика ",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Suppliers\Query\GetCounteragentsQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array")
     *     }
     * )
     */
    public function getCounteragentsAction(int $id, Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetCounteragentsQuery($request->query->all(), ['id' => $id,]), $list);

        return $list;
    }

    /**
     * @VIA\Get(
     *     path="/supplierCounteragents/forOrdersShipping/",
     *     description="Получение контрагентов поставщика для счета",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Suppliers\Query\GetCounteragentsForSupplyQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="SupplyBundle\Bus\Suppliers\Query\DTO\CounteragentsForSupply")
     *     }
     * )
     */
    public function getCounteragentsForSupplyAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetCounteragentsForSupplyQuery($request->query->all()), $suppliers);

        return $suppliers;
    }
}