<?php 

namespace ContentBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use ContentBundle\Bus\MeasureUnit\Query;
use ContentBundle\Bus\MeasureUnit\Command;

/**
 * @VIA\Section("Единицы измерения")
 */
class MeasureUnitController extends RestController
{
    /**
     * @VIA\Post(
     *      path="/measureUnits/",
     *      description="Создание единицы измерения",
     *      parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ContentBundle\Bus\MeasureUnit\Command\CreateCommand")
     *      }
     * )
     * @VIA\Response(
     *      status=201,
     *      properties={
     *          @VIA\Property(name="id", type="integer")
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
     *     path="/measureUnits/{id}/",
     *     description="Редактирование единицы измерения",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\MeasureUnit\Command\UpdateCommand")
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
     * @VIA\Patch(
     *     path="/measureUnits/{id}/",
     *     description="Объединение единиц измерения",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\MeasureUnit\Command\MergeCommand")
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
     *     path="/measureUnits/{id}/",
     *     description="Удаление единицы измерения",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\MeasureUnit\Command\DeleteCommand")
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