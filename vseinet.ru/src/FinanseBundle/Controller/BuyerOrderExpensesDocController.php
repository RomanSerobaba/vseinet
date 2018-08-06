<?php

namespace FinanseBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use FinanseBundle\Bus\BuyerOrderExpensesDoc\Query;
use FinanseBundle\Bus\BuyerOrderExpensesDoc\Command;

/**
 * @VIA\Section("Расходы - Выставленные счета")
 */
class BuyerOrderExpensesDocController extends RestController
{

    /**
     * @VIA\Get(
     *     path="/buyerOrderExpenses/",
     *     description="Выставленные счета - Получить список документов",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="FinanseBundle\Bus\BuyerOrderExpensesDoc\Query\ListQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="FinanseBundle\Bus\BuyerOrderExpensesDoc\Query\DTO\DocumentList")
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
     *     path="/buyerOrderExpenses/statuses/",
     *     description="Выставленные счета - Получить список статусов документа",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="FinanseBundle\Bus\BuyerOrderExpensesDoc\Query\StatusesQuery")
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
     *     path="/buyerOrderExpenses/",
     *     description="Выставленные счета - Создать документ",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="FinanseBundle\Bus\BuyerOrderExpensesDoc\Command\CreateCommand")
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
     *     path="/buyerOrderExpenses/{id}/",
     *     description="Выставленные счета - Получить шапку документа",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="FinanseBundle\Bus\BuyerOrderExpensesDoc\Query\ItemQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="FinanseBundle\Bus\BuyerOrderExpensesDoc\Query\DTO\DocumentHead")
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
     *     path="/buyerOrderExpenses/{id}/",
     *     description="Выставленные счета - Изменить шапку документа",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="FinanseBundle\Bus\BuyerOrderExpensesDoc\Command\UpdateCommand")
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
     *     path="/buyerOrderExpenses/{id}/",
     *     description="Выставленные счета - Удалить документ",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="FinanseBundle\Bus\BuyerOrderExpensesDoc\Command\DeleteCommand")
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
     *     path="/buyerOrderExpenses/{id}/statusCode/",
     *     description="Выставленные счета - Установить статус докмуента",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="FinanseBundle\Bus\BuyerOrderExpensesDoc\Command\StatusCommand")
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
