<?php 

namespace ContentBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use ContentBundle\Bus\DetailValue\Command;
use AppBundle\Annotation as VIA;

/**
 * @VIA\Section("Характеристики")
 */
class DetailValueController extends RestController
{
    /**
     * @VIA\Post(
     *     path="/detailValues/",
     *     description="Создание значения характеристики",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\DetailValue\Command\CreateCommand")
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
     *     path="/detailValues/{id}/",
     *     description="Редактирование значения характеристики",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\DetailValue\Command\UpdateCommand")
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
     * @VIA\Put(
     *     path="/detailValues/{id}/isVerified/",
     *     description="Подтверждение значения характеристики",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\DetailValue\Command\SetIsVerifiedCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function setIsVerifiedAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetIsVerifiedCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @VIA\Patch(
     *     path="/detailValues/{id}/",
     *     description="Объединение значений характеристики",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\DetailValue\Command\MergeCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function mergeAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\MergeCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @VIA\Delete(
     *     path="/detailValues/{id}/",
     *     description="Удаление значения характеристики",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\DetailValue\Command\DeleteCommand")
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
}