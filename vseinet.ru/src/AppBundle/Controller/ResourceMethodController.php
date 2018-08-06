<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use AppBundle\Bus\ResourceMethod\Query;
use AppBundle\Bus\ResourceMethod\Command;

/**
 * @VIA\Section("Права доступа")
 */
class ResourceMethodController extends RestController
{
    /**
     * @VIA\Get(
     *     path="/api/v1/app/resource/method/list/",
     *     title="Получение списка методов ресурса",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="AppBundle\Bus\ResourceMethod\Query\GetListQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="AppBundle\Bus\ResourceMethod\Query\DTO\Method")
     *     }
     * )
     */
    public function getListAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetListQuery($request->query->all()), $methods);

        return $methods;
    }

    /**
     * @VIA\Get(
     *     path="/api/v1/app/resource/method/role/codex/",
     *     title="Получение свода прав доступа к методам ресурса по ролям",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="AppBundle\Bus\ResourceMethod\Query\GetRoleCodexQuery")
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
     *     path="/api/v1/app/resource/method/user/codex/",
     *     title="Получение свода прав доступа к методам ресурса для пользователя",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="AppBundle\Bus\ResourceMethod\Query\GetUserCodexQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="AppBundle\Bus\ResourceMethod\Query\DTO\UserCodexItem")
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
     *     path="/api/v1/app/resource/method/{id}/",
     *     title="Получение метода ресурса",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="AppBundle\Bus\ResourceMethod\Query\GetQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="AppBundle\Bus\ResourceMethod\Query\DTO\Method")
     *     }
     * )
     */
    public function getAction(int $id)
    {
        $this->get('query_bus')->handle(new Query\GetQuery(['id' => $id]), $method);

        return $method;
    } 

    /**
     * @VIA\Post(
     *     path="/api/v1/app/resource/method/new/",
     *     title="Создание метода ресурса",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="AppBundle\Bus\ResourceMethod\Command\CreateCommand")
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
     *     path="/api/v1/app/resource/method/{id}/edit/",
     *     title="Редактирование метода ресурса",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="AppBundle\Bus\ResourceMethod\Command\UpdateCommand")
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
     *     path="/api/v1/app/resource/method/{id}/remove/",
     *     title="Удаление метода ресурса",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="AppBundle\Bus\ResourceMethod\Command\DeleteCommand")
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
     *     path="/api/v1/app/resource/method/{methodId}/allow/role/{subroleId}/",
     *     title="Разрешить доступ к методу ресурса для роли",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="AppBundle\Bus\ResourceMethod\Command\AllowRoleCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function allowRoleAction(int $methodId, int $subroleId)
    {
        $this->get('command_bus')->handle(new Command\AllowRoleCommand(['methodId' => $methodId, 'subroleId' => $subroleId]));
    }  

    /**
     * @VIA\Patch(
     *     path="/api/v1/app/resource/method/{methodId}/deny/role/{subroleId}/",
     *     title="Запретить доступ к методу ресурса для роли",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="AppBundle\Bus\ResourceMethod\Command\DenyRoleCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function denyRoleAction(int $methodId, int $subroleId)
    {
        $this->get('command_bus')->handle(new Command\DenyRoleCommand(['methodId' => $methodId, 'subroleId' => $subroleId]));
    } 

    /**
     * @VIA\Patch(
     *     path="/api/v1/app/resource/method/{methodId}/allow/user/{userId}/",
     *     title="Разрешить доступ к методу ресурса для пользователя",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="AppBundle\Bus\ResourceMethod\Command\AllowUserCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function allowUserAction(int $methodId, int $userId)
    {
        $this->get('command_bus')->handle(new Command\AllowUserCommand(['methodId' => $methodId, 'userId' => $userId]));
    }  

    /**
     * @VIA\Patch(
     *     path="/api/v1/app/resource/method/{methodId}/deny/user/{userId}/",
     *     title="Запретить доступ к методу ресурса для пользователя",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="AppBundle\Bus\ResourceMethod\Command\DenyUserCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function denyUserAction(int $methodId, int $userId)
    {
        $this->get('command_bus')->handle(new Command\DenyUserCommand(['methodId' => $methodId, 'userId' => $userId]));
    }     
}