<?php

namespace ContentBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use ContentBundle\Bus\Manager\Command;
use AppBundle\Annotation as VIA;

/**
 * @VIA\Section("Контент-менеджеры")
 */
class ManagerController extends RestController
{
    /**
     * @VIA\Post(
     *     path="/contentManagers/",
     *     description="Создание контент-менеджера",
     *     parameters={
     *         @VIA\Parameter(model="ContentBundle\Bus\Manager\Command\CreateCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function newAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\CreateCommand($request->request->all()));
    }

    /**
     * @VIA\Put(
     *     path="/contentManagers/{userId}/",
     *     description="Редактирование контент-менеджера",
     *     parameters={
     *         @VIA\Parameter(model="ContentBundle\Bus\Manager\Command\UpdateCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function editAction(int $userId, Request $request)
    {
        $this->get('command_bus')->handle(new Command\UpdateCommand($request->request->all(), ['userId' => $userId]));
    }

    /**
     * @VIA\Delete(
     *     path="/contentManagers/{userId}/",
     *     description="Удаление контент-менеджера",
     *     parameters={
     *         @VIA\Parameter(model="ContentBundle\Bus\Manager\Command\DeleteCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function removeAction(int $userId)
    {
        $this->get('command_bus')->handle(new Command\DeleteCommand(['userId' => $userId]));
    }
}