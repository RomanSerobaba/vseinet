<?php

namespace ReservesBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use ReservesBundle\Bus\GoodsIssueDoc\Query;
use ReservesBundle\Bus\GoodsIssueDoc\Command;

/**
 * @VIA\Section("Претензии по товарам - Претензии")
 */
class GoodsIssueDocController extends RestController
{

    /**
     * @VIA\Get(
     *     path="/goodsIssues/",
     *     description="Получить список документов",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsIssueDoc\Query\ListQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="ReservesBundle\Bus\GoodsIssueDoc\Query\DTO\DocumentList")
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
     *     path="/goodsIssues/statuses/",
     *     description="Получить список статусов документа",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsIssueDoc\Query\StatusesQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="ReservesBundle\Bus\GoodsIssueDoc\Query\DTO\Statuses")
     *     }
     * )
     */
    public function listStatusesAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\StatusesQuery($request->query->all()), $items);

        return $items;
    }

    /**
     * @VIA\Get(
     *     path="/goodsIssues/{id}/operations/",
     *     description="Получить список операций по претензии",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsIssueDoc\Query\ListRelatedElementsQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="ReservesBundle\Bus\GoodsIssueDoc\Query\DTO\DocumentRelatedElement")
     *     }
     * )
     */
    public function listOperationsAction(int $id, Request $request)
    {
        $this->get('query_bus')->handle(new Query\ListRelatedElementsQuery($request->query->all(), ['id' => $id]), $items);

        return $items;
    }

    /**
     * @VIA\Get(
     *     path="/goodsIssues/{id}/",
     *     description="Получить шапку документа",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsIssueDoc\Query\ItemQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="ReservesBundle\Bus\GoodsIssueDoc\Query\DTO\DocumentHead")
     *     }
     * )
     */
    public function getAction(int $id)
    {
        $this->get('query_bus')->handle(new Query\ItemQuery(['id' => $id]), $item);
        return $item;
    }

    /**
     * @VIA\Post(
     *     path="/goodsIssues/",
     *     description="Создать документ",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsIssueDoc\Command\CreateCommand")
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

        $this->get('command_bus')->handle(new Command\CreateCommand($request->request->all(), ['uuid' => $uuid]));

        return ['id' => $this->get('uuid.manager')->loadId($uuid)];
    }

    /**
     * @VIA\Put(
     *     path="/goodsIssues/{id}/",
     *     description="Изменить шапку документа",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsIssueDoc\Command\UpdateCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201
     * )
     */
    public function setAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\UpdateCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @VIA\Delete(
     *     path="/goodsIssues/{id}/",
     *     description="Удалить документ",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsIssueDoc\Command\DeleteCommand")
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
     *     path="/goodsIssues/{id}/statusCode/",
     *     description="Установить статус докмуента",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsIssueDoc\Command\StatusCommand")
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

    /**
     * @VIA\Get(
     *     path="/orderItems/forIssues/",
     *     description="Получить список товаров по номеру заказа",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsIssueDoc\Query\OrderItemsForIssueQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="ReservesBundle\Bus\GoodsIssueDoc\Query\DTO\OrderItemsForIssueDTO")
     *     }
     * )
     */
    public function getOrderItemsForIssueAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\OrderItemsForIssueQuery($request->query->all()), $items);

        return $items;
    }

}
