<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use AppBundle\Bus\Resource\Query;
use AppBundle\Bus\Resource\Command;

/**
 * @VIA\Section("Права доступа")
 */
class ResourceController extends RestController
{
    /**
     * @VIA\Get(
     *     path="/api/v1/app/resource/list/",
     *     title="Получение списка ресурсов",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="AppBundle\Bus\Resource\Query\GetListQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="AppBundle\Entity\Resource")
     *     }
     * )
     */
    public function getListAction()
    {
        $this->get('query_bus')->handle(new Query\GetListQuery(), $resources);

        return $resources;
    }

    /**
     * @VIA\Get(
     *     path="/api/v1/app/resource/role/codex/",
     *     title="Получение свода прав доступа к ресурсам по ролям",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="AppBundle\Bus\Resource\Query\GetRoleCodexQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array")
     *     }
     * )
     */
    public function getRoleCodexAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetRoleCodexQuery($request->query->all()), $codex);

        return $codex;
    }

    /**
     * @VIA\Get(
     *     path="/api/v1/app/resource/user/codex/",
     *     title="Получение свода прав доступа к ресурсам для пользователя",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="AppBundle\Bus\Resource\Query\GetUserCodexQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="AppBundle\Bus\Resource\Query\DTO\UserCodexItem")
     *     }
     * )
     */
    public function getUserCodexAction()
    {
        $this->get('query_bus')->handle(new Query\GetUserCodexQuery(), $codex);

        return $codex;
    }

    /**
     * @VIA\Get(
     *     path="/api/v1/app/resource/{id}/",
     *     title="Получение ресурса",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="AppBundle\Bus\Resource\Query\GetQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="AppBundle\Entity\Resource")
     *     }
     * )
     */
    public function getAction(int $id)
    {
        $this->get('query_bus')->handle(new Query\GetQuery(['id' => $id]), $resource);

        return $resource;
    }

    /**
     * @deprecated
     * @VIA\Get(
     *     path="/api/v1/appResources/{id}/accessRights/",
     *     title="Получение списка прав доступа к ресурсам",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="AppBundle\Bus\Resource\Query\GetAccessRightsQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array<string>")
     *     }
     * )
     */
    public function getAccessRightsAction(int $id)
    {
        $this->get('query_bus')->handle(new Query\GetAccessRightsQuery(['id' => $id]), $rights);

        return $rights;
    } 

    /**
     * @VIA\Post(
     *     path="/api/v1/app/resource/new/",
     *     title="Создание ресурса",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="AppBundle\Bus\Resource\Command\CreateCommand")
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
     *     path="/api/v1/app/resource/{id}/edit/",
     *     title="Редактирование ресурса",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="AppBundle\Bus\Resource\Command\UpdateCommand")
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
     *     path="/api/v1/app/resource/{id}/remove/",
     *     title="Удаление ресурса",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="AppBundle\Bus\Resource\Command\DeleteCommand")
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
     *     path="/api/v1/app/resource/{id}/sort/",
     *     title="Сортировка ресурсов",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="AppBundle\Bus\Resource\Command\SortCommand")
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

    /**
     * @VIA\Patch(
     *     path="/api/v1/app/resource/{resourceId}/allow/role/{subroleId}/",
     *     title="Разрешить доступ к ресурсу для роли",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="AppBundle\Bus\Resource\Command\AllowRoleCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function allowRoleAction(int $resourceId, int $subroleId)
    {
        $this->get('command_bus')->handle(new Command\AllowRoleCommand(['resourceId' => $resourceId, 'subroleId' => $subroleId]));
    }  

    /**
     * @VIA\Patch(
     *     path="/api/v1/app/resource/{resourceId}/deny/role/{subroleId}/",
     *     title="Запретить доступ к ресурсу для роли",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="AppBundle\Bus\Resource\Command\DenyRoleCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function denyRoleAction(int $resourceId, int $subroleId)
    {
        $this->get('command_bus')->handle(new Command\DenyRoleCommand(['resourceId' => $resourceId, 'subroleId' => $subroleId]));
    } 

    /**
     * @VIA\Patch(
     *     path="/api/v1/app/resource/{resourceId}/allow/user/{userId}/",
     *     title="Разрешить доступ к ресурсу для пользователя",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="AppBundle\Bus\Resource\Command\AllowUserCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function allowUserAction(int $resourceId, int $userId)
    {
        $this->get('command_bus')->handle(new Command\AllowUserCommand(['resourceId' => $resourceId, 'userId' => $userId]));
    }  

    /**
     * @VIA\Patch(
     *     path="/api/v1/app/resource/{resourceId}/deny/user/{userId}/",
     *     title="Запретить доступ к ресурсу для пользователя",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="AppBundle\Bus\Resource\Command\DenyUserCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function denyUserAction(int $resourceId, int $userId)
    {
        $this->get('command_bus')->handle(new Command\DenyUserCommand(['resourceId' => $resourceId, 'userId' => $userId]));
    }     
}