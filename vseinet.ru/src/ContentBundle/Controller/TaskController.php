<?php

namespace ContentBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use ContentBundle\Bus\Task\Query;
use ContentBundle\Bus\Task\Command;
use AppBundle\Annotation as VIA;

/**
 * @VIA\Section("Контент-менеджеры")
 */
class TaskController extends RestController
{
    /**
     * @VIA\Get(
     *     path="/contentTasks/",
     *     description="Получение списка заданий контент-менеджера",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Task\Query\GetListQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ContentBundle\Bus\Task\Query\DTO\Task")
     *     }
     * )
     */
    public function getListAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetListQuery($request->query->all()), $tasks);

        return $tasks;
    }

    /**
     * @deprecated
     * @VIA\Get(
     *     path="/contentTasks/{id}/",
     *     description="Получение задания контент-менеджера",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Task\Query\GetQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="ContentBundle\Entity\Task")
     *     }
     * )
     */
    public function getAction(int $id, Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetQuery(['id' => $id]), $task);

        return $task;
    }

    /**
     * @VIA\Post(
     *     path="/contentTasks/",
     *     description="Создание задания контент-манеджера",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Task\Command\CreateCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201,
     *     properties={
     *         @VIA\Property(name="id", type="integer")
     *     }
     * )
     */
    public function newAction(Request $request)
    {
        $uuid = $this->get('uuid.manager')->generate();
        $this->get('command_bus')->handle(new Command\CreateCommand($request->request->all(), ['uuid' => $uuid]));

        return [
            'id' => $this->get('uuid.manager')->loadId($uuid),
        ];
    }

    /**
     * @VIA\Delete(
     *     path="/contentTasks/",
     *     description="Удаление задания контент-менеджера",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Task\Command\DeleteCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function removeAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\DeleteCommand($request->request->all()));
    }
}