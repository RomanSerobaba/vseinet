<?php

namespace ContentBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use ContentBundle\Bus\ManagerGroup\Command;
use AppBundle\Annotation as VIA;

/**
 * @VIA\Section("Контент-менеджеры")
 */
class ManagerGroupController extends RestController
{
    /**
     * @VIA\Post(
     *      path="/contentManagerGroups/",
     *      description="Создание группы контент-менеджеров",
     *      parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ContentBundle\Bus\ManagerGroup\Command\CreateCommand")
     *      }
     * )
     * @VIA\Response(
     *      status=201,
     *      properties={
     *          @VIA\Parameter(name="id", type="integer")
     *      }
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
     *      path="/contentManagerGroups/{id}/",
     *      description="Редактирование группы контент-менеджеров",
     *      parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ContentBundle\Bus\ManagerGroup\Command\UpdateCommand")
     *      }
     * )
     * @VIA\Response(
     *      status=204
     * )
     */
    public function editAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\UpdateCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @VIA\Delete(
     *      path="/contentManagerGroups/{id}/",
     *      description="Удаление группы контент-менеджеров",
     *      parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ContentBundle\Bus\ManagerGroup\Command\DeleteCommand")
     *      }
     * )
     * @VIA\Response(
     *      status=204
     * )
     */
    public function removeAction(int $id)
    {
        $this->get('command_bus')->handle(new Command\DeleteCommand(['id' => $id]));
    }
}