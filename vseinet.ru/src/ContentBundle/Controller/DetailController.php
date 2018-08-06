<?php 

namespace ContentBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use ContentBundle\Bus\Detail\Query;
use ContentBundle\Bus\Detail\Command;

/**
 * @VIA\Section("Характеристики")
 */
class DetailController extends RestController
{
    /**
     * @VIA\Get(
     *     path="/detailTypes/",
     *     description="Получение списка типов характеристик",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Detail\Query\GetTypesQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ContentBundle\Entity\DetailType")
     *     }
     * )
     */
    public function getTypesAction()
    {
        $this->get('query_bus')->handle(new Query\GetTypesQuery(), $types);

        return $types;
    } 

    /**
     * @VIA\Get(
     *     path="/details/",
     *     description="Получение списка характеристик",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Detail\Query\GetListQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ContentBundle\Bus\Detail\Query\DTO\Detail")
     *     }
     * )
     */
    public function getListAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetListQuery($request->query->all()), $details);

        return $details;
    } 

    /**
     * @todo: not used
     * @VIA\Get(
     *     path="/detail/by/ids/",
     *     description="Получение характеристик по списку id",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Detail\Query\GetByIdsQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ContentBundle\Bus\Detail\Query\DTO\DetailItem")
     *     }
     * )
     */
    public function getByIdsAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetByIdsQuery($request->query->all()), $details);

        return $details;
    } 

    /**
     * @VIA\Get(
     *     path="/details/{id}/",
     *     requirements={"id"="\d+"},
     *     description="Получение характеристики",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Detail\Query\GetQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="ContentBundle\Bus\Detail\Query\DTO\Detail")
     *     }
     * )
     */
    public function getAction(int $id)
    {
        $this->get('query_bus')->handle(new Query\GetQuery(['id' => $id]), $detail);

        return $detail;
    }

    /**
     * @VIA\Post(
     *     path="/details/",
     *     description="Создание характеристик\и",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Detail\Command\CreateCommand")
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
     *     path="/details/{id}/",
     *     description="Редактирование характеристики",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Detail\Command\UpdateCommand")
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
     *     path="/details/{id}/sortOrder/",
     *     description="Сортировка характеристик",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Detail\Command\SortCommand")
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
     * @todo
     * @VIA\Patch(
     *     path="/details/{id}/",
     *     description="Объединение характеристик",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Detail\Command\MergeCommand")
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
     *     path="/details/{id}/",
     *     description="Удаление характеристики",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Detail\Command\DeleteCommand")
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
     *     path="/details/{id}/type/",
     *     description="Конвертирование типа характеристики",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Detail\Command\ConvertCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function convertAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\ConvertCommand($request->request->all(), ['id' => $id]));
    }
}