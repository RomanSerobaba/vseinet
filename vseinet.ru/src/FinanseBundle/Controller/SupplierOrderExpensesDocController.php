<?php

namespace FinanseBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use FinanseBundle\Bus\SupplierOrderExpensesDoc\Query;
use FinanseBundle\Bus\SupplierOrderExpensesDoc\Command;

/**
 * @VIA\Section("Расходы - Оплата счета поставщика")
 */
class SupplierOrderExpensesDocController extends RestController
{

    /**
     * @VIA\Get(
     *     path="/supplierOrderExpenses/",
     *     description="Оплата счета поставщика - Получить список документов",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="FinanseBundle\Bus\SupplierOrderExpensesDoc\Query\ListQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="FinanseBundle\Bus\SupplierOrderExpensesDoc\Query\DTO\DocumentList")
     *     }
     * )
     */
    public function listAction(Request $request)
    {

        $this->get('query_bus')->handle(new Query\ListQuery($request->query->all()), $items);

        return $items;

    }

    /**
     * @VIA\Get(
     *     path="/supplierOrderExpenses/statuses/",
     *     description="Оплата счета поставщика - Получить список статусов документа",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="FinanseBundle\Bus\SupplierOrderExpensesDoc\Query\StatusesQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="DocumentBundle\Prototipe\StatusesDTO")
     *     }
     * )
     */
    public function listStatusesAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\StatusesQuery($request->query->all()), $items);

        return $items;
    }

    /**
     * @VIA\Post(
     *     path="/supplierOrderExpenses/",
     *     description="Оплата счета поставщика - Создать документ",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="FinanseBundle\Bus\SupplierOrderExpensesDoc\Command\CreateCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201,
     *     properties={
     *         @VIA\Property(name="id", type="integer")
     *     }
     * )
     */
    public function createAction(Request $request)
    {
        $uuid = $this->get('uuid.manager')->generate();
        
        $this->get('command_bus')->handle(new Command\CreateCommand($request->request->all(), [
            'createdBy' => $this->getUser()->getId(), 
            'uuid' => $uuid]));

        return [
            'id' => $this->get('uuid.manager')->loadId($uuid)
        ];
    }

    /**
     * @VIA\Get(
     *     path="/supplierOrderExpenses/{id}/",
     *     description="Оплата счета поставщика - Получить шапку документа",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="FinanseBundle\Bus\SupplierOrderExpensesDoc\Query\ItemQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="FinanseBundle\Bus\SupplierOrderExpensesDoc\Query\DTO\DocumentHead")
     *     }
     * )
     */
    public function getAction(int $id)
    {
        $this->get('query_bus')->handle(new Query\ItemQuery(['id' => $id]), $item);
        return $item;
    }

    /**
     * @VIA\Put(
     *     path="/supplierOrderExpenses/{id}/",
     *     description="Оплата счета поставщика - Изменить шапку документа",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="FinanseBundle\Bus\SupplierOrderExpensesDoc\Command\UpdateCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201
     * )
     */
    public function updateAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\UpdateCommand($request->request->all(), [
            'id' => $id
        ]));
    }

    /**
     * @VIA\Delete(
     *     path="/supplierOrderExpenses/{id}/",
     *     description="Оплата счета поставщика - Удалить документ",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="FinanseBundle\Bus\SupplierOrderExpensesDoc\Command\DeleteCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function delAction(int $id)
    {
        $this->get('command_bus')->handle(new Command\DeleteCommand(['id' => $id]));
    }

    /**
     * @VIA\Put(
     *     path="/supplierOrderExpenses/{id}/statusCode/",
     *     description="Оплата счета поставщика - Установить статус докмуента",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="FinanseBundle\Bus\SupplierOrderExpensesDoc\Command\StatusCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201
     * )
     */
    public function setStatusAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\StatusCommand($request->request->all(), ['id' => $id]));
    }

}
