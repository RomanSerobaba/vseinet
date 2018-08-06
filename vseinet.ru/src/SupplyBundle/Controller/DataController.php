<?php

namespace SupplyBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use SupplyBundle\Bus\Data\Query;
use SupplyBundle\Bus\Data\Command;

/**
 * @VIA\Section("Данные по счетам")
 */
class DataController extends RestController
{
    /**
     * @VIA\Get(
     *     path="/supplies/",
     *     description="Получить список счетов",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Data\Query\GetSupplierWithInvoicesQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="SupplyBundle\Bus\Data\Query\DTO\SupplierWithInvoices")
     *     }
     * )
     */
    public function getListAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetSupplierWithInvoicesQuery($request->query->all()), $list);

        return $list;
    }

    /**
     * @VIA\Get(
     *     path="/supplyItems/forShipping/",
     *     description="Получение списка товаров и позиций в счете",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Data\Query\GetSupplyItemsForShippingQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", name="products", model="SupplyBundle\Bus\Data\Query\DTO\SupplyItemsForShippingProducts"),
     *         @VIA\Property(type="array", name="orderItems", model="SupplyBundle\Bus\Data\Query\DTO\SupplyItemsForShippingOrders")
     *     }
     * )
     */
    public function getSupplyItemsForShippingAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetSupplyItemsForShippingQuery($request->query->all()), $list);

        return $list;
    }

    /**
     * @VIA\Get(
     *     path="/supplies/{id}/candidates/",
     *     description="Получение списка позиций, которые можно добавить в счет",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Data\Query\GetCandidatesQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", name="products", model="SupplyBundle\Bus\Data\Query\DTO\SupplyItemsForShippingProducts"),
     *         @VIA\Property(type="array", name="orderItems", model="SupplyBundle\Bus\Data\Query\DTO\SupplyItemsForShippingOrders")
     *     }
     * )
     */
    public function getCandidatesQueryAction(int $id)
    {
        $this->get('query_bus')->handle(new Query\GetCandidatesQuery(['id' => $id,]), $list);

        return $list;
    }

    /**
     * @VIA\Post(
     *     path="/supplies/",
     *     description="Создать новый счет",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="SupplyBundle\Bus\Data\Command\CreateSupplierInvoiceCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201,
     *     properties={
     *         @VIA\Property(name="id", type="integer")
     *     }
     * )
     */
    public function createSupplierInvoiceAction(Request $request)
    {
        $uuid = $this->get('uuid.manager')->generate();
        $this->get('command_bus')->handle(new Command\CreateSupplierInvoiceCommand($request->request->all(), ['uuid' => $uuid,]));

        return [
            'id' => $this->get('uuid.manager')->loadId($uuid),
        ];
    }

    /**
     * @VIA\Get(
     *     path="/supplies/{id}/",
     *     description="Получение информации по счету",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Data\Query\GetSupplierInvoiceQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="SupplyBundle\Bus\Data\Query\DTO\SupplierWithInvoices")
     *     }
     * )
     */
    public function getSupplierInvoiceAction(int $id)
    {
        $this->get('query_bus')->handle(new Query\GetSupplierInvoiceQuery(['id' => $id,]), $supplierInvoice);

        return $supplierInvoice;
    }

    /**
     * @VIA\Put(
     *     path="/supplies/{id}/supplierCounteragent/",
     *     description="Изменить контрагента поставщика в счете",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Data\Command\ChangeSupplierInvoiceCounteragentCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function changeCounteragentAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\ChangeSupplierInvoiceCounteragentCommand($request->request->all(), ['id' => $id,]));
    }

    /**
     * @VIA\Put(
     *     path="/supplies/{id}/supplierInvoiceNumber/",
     *     description="Изменить номер счета",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Data\Command\ChangeSupplierInvoiceNumberCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function changeSupplierInvoiceNumberAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\ChangeSupplierInvoiceNumberCommand($request->request->all(), ['id' => $id,]));
    }

    /**
     * @VIA\Delete(
     *     path="/supplies/{id}/",
     *     description="Удалить счет",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Data\Command\DeleteSupplierInvoiceCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function deleteSupplierInvoiceAction(int $id)
    {
        $this->get('command_bus')->handle(new Command\DeleteSupplierInvoiceCommand(['id' => $id,]));
    }

    /**
     * @VIA\Put(
     *     path="/supplies/{id}/closing/",
     *     description="Закрыть счет",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Data\Command\CloseSupplierInvoiceCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function closeSupplierInvoiceAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\CloseSupplierInvoiceCommand($request->request->all(), ['id' => $id,]));
    }

    /**
     * @VIA\Post(
     *     path="/supplies/{id}/file/",
     *     description="Загрузка счета от поставщика",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Data\Query\UploadingQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="SupplyBundle\Bus\Data\Query\DTO\Uploading", type="array")
     *     }
     * )
     */
    public function uploadingAction(int $id, Request $request)
    {
        $this->get('query_bus')->handle(new Query\UploadingQuery($request->query->all(), [
            'id' => $id,
            'filename' => $request->files->get('filename'),
        ]), $list);

        return $list;
    }

    /**
     * @VIA\Put(
     *     path="/supplies/{id}/items/",
     *     description="Добавить позиции в счет",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="SupplyBundle\Bus\Data\Command\AddInvoiceItemsCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function addItemsAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\AddInvoiceItemsCommand($request->request->all(), ['id' => $id,]));
    }

    /**
     * @VIA\Get(
     *     path="/supplies/for1C/",
     *     description="Получение накладных",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Data\Query\GetFor1CQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array")
     *     }
     * )
     */
    public function getFor1CAction(Request $request)
    {

        $this->get('query_bus')->handle(new Query\GetFor1CQuery($request->query->all()), $list);

        return $list;
    }

    /**
     * @VIA\Put(
     *     path="/supplies/{id}/comment/",
     *     description="Редактирование комментария",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Data\Command\EditSupplyInvoiceCommentCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function setCommentAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\EditSupplyInvoiceCommentCommand($request->request->all(), ['id' => $id,]));
    }
}