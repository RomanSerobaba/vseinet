<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use AppBundle\Bus\ResourceGroup\Query;
use AppBundle\Bus\ResourceGroup\Command;

/**
 * @VIA\Section("Права доступа")
 */
class ResourceGroupController extends RestController
{
    /**
     * @VIA\Get(
     *     path="/api/v1/app/resource/group/list/",
     *     title="Получение списка групп ресурсов",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="AppBundle\Bus\ResourceGroup\Query\GetListQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="AppBundle\Entity\ResourceGroup")
     *     }
     * )
     */
    public function getListAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetListQuery($request->query->all()), $groups);

        return $groups;
    }

    /**
     * @VIA\Get(
     *     path="/api/v1/app/resource/group/{id}/",
     *     title="Получение группы ресурсов",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="AppBundle\Bus\ResourceGroup\Query\GetQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="AppBundle\Entity\ResourceGroup")
     *     }
     * )
     */
    public function getAction(int $id)
    {
        $this->get('query_bus')->handle(new Query\GetQuery(['id' => $id]), $group);

        return $group;
    }

    /**
     * @VIA\Post(
     *     path="/api/v1/app/resource/group/new/",
     *     title="Создание группы ресурсов",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="AppBundle\Bus\ResourceGroup\Command\CreateCommand")
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
     * @VIA\Put(
     *     path="/api/v1/app/resource/group/{id}/edit/",
     *     title="Редактирование группы ресурсов",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="AppBundle\Bus\ResourceGroup\Command\UpdateCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function editAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\UpdateCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @VIA\Delete(
     *     path="/api/v1/app/resource/group/{id}/remove/",
     *     title="Удаление группы ресурсов",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="AppBundle\Bus\ResourceGroup\Command\DeleteCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function removeAction(int $id)
    {
        $this->get('command_bus')->handle(new Command\DeleteCommand(['id' => $id]));
    }

    /**
     * @VIA\Patch(
     *     path="/api/v1/app/resource/group/{id}/sort/",
     *     title="Сортировка групп ресурсов",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="AppBundle\Bus\ResourceGroup\Command\SortCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function sortAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SortCommand($request->request->all(), ['id' => $id]));
    }
}