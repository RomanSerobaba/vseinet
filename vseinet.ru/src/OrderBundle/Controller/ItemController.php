<?php

namespace OrderBundle\Controller;

use AppBundle\Controller\RestController;
use AppBundle\Annotation as VIA;
use Symfony\Component\HttpFoundation\Request;
use OrderBundle\Bus\Item\Query;
use OrderBundle\Bus\Item\Command;
use SupplyBundle\Component\OrderComponent;

/**
 * @VIA\Section("Сущности заказа")
 */
class ItemController extends RestController
{
    /**
     * @VIA\Put(
     *     path="/orderItems/{id}/quantity/",
     *     description="Редактирование количества позиции заказа",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="OrderBundle\Bus\Item\Command\ChangeQuantityCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function changeQuantityAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\ChangeQuantityCommand($request->request->all(), ['id' => $id, 'type' => OrderComponent::TYPE_ORDER,]));
    }

    /**
     * @VIA\Post(
     *     path="/orderItems/{id}/comments/",
     *     description="Сохранение комментария к позиции заказа",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="OrderBundle\Bus\Item\Command\AddCommentCommand"),
     *     }
     * )
     * @VIA\Response(
     *     status=201,
     *     properties={
     *         @VIA\Property(name="id", type="integer")
     *     }
     * )
     */
    public function addCommentAction(int $id, Request $request)
    {
        $uuid = $this->get('uuid.manager')->generate();
        $this->get('command_bus')->handle(new Command\AddCommentCommand($request->request->all(), ['id' => $id, 'uuid' => $uuid,]));

        return [
            'id' => $this->get('uuid.manager')->loadId($uuid),
        ];
    }

    /**
     * @VIA\Get(
     *     path="/orderItems/{id}/comments/",
     *     description="Показать комментарии к позиции заказа",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="OrderBundle\Bus\Item\Query\GetCommentsQuery"),
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="OrderBundle\Bus\Item\Query\DTO\GetComments")
     *     }
     * )
     */
    public function commentsAction(int $id)
    {
        $this->get('query_bus')->handle(new Query\GetCommentsQuery(['id' => $id,]), $comments);

        return $comments;
    }

    /**
     * @VIA\Get(
     *     path="/orderItemStatuses/",
     *     description="Получение списка статусов позиций заказа",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="OrderBundle\Bus\Item\Query\GetStatusesQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="OrderBundle\Bus\Item\Query\DTO\Status")
     *     }
     * )
     */
    public function statusesAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetStatusesQuery($request->query->all()), $statuses);

        return $statuses;
    }

    /**
     * @VIA\Get(
     *     path="/orderItems/{id}/statusesLogs/",
     *     description="Получение истории изменения статусов позиции",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="OrderBundle\Bus\Item\Query\GetStatusesLogsQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="OrderBundle\Bus\Item\Query\DTO\StatusesLog")
     *     }
     * )
     */
    public function geStatusesLogsAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetStatusesLogsQuery($request->query->all()), $logs);

        return $logs;
    }
}