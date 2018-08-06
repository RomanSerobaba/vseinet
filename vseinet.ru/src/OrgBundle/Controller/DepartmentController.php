<?php

namespace OrgBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use OrgBundle\Bus\Department\Command;
use OrgBundle\Bus\Department\Query;

/**
 * @VIA\Section("Структура организации - Подразделения")
 */
class DepartmentController extends RestController
{
    /**
     * @VIA\Get(
     *     path="/orgStructure/",
     *     description="Получить нормализованное дерево подразделений и сотрудников",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Department\Query\GetStructureQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", name="departments", model="OrgBundle\Bus\Department\Query\DTO\Department"),
     *         @VIA\Property(type="array", name="employees",   model="OrgBundle\Bus\Department\Query\DTO\Employee")
     *     }
     * )
     */
    public function GetStructureAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetStructureQuery($request->query->all()), $items);

        return $items;
    }

    /**
     * @VIA\Get(
     *     path="/departments/foundResults/",
     *     description="Поиск подразделений",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Department\Query\FoundResultsQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", name="departments", model="OrgBundle\Bus\Department\Query\DTO\DepartmentResult")
     *     }
     * )
     */
    public function FoundResultsAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\FoundResultsQuery($request->query->all()), $departments);

        return $departments;
    }

    /**
     * @VIA\Get(
     *     path="/departments/{id}/",
     *     description="Получить информацию о подразделении",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Department\Query\GetDepartmentInfoQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="OrgBundle\Bus\Department\Query\DTO\DepartmentInfo")
     *     }
     * )
     */
    public function GetDepartmentInfoAction(int $id, Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetDepartmentInfoQuery($request->query->all(), ['id' => $id]), $info);

        return $info;
    }

    /**
     * @VIA\Put(
     *     path="/departments/{id}/",
     *     description="Редактировать информацию о подразделении",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Department\Command\UpdateDepartmentInfoCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function UpdateDepartmentInfoAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\UpdateDepartmentInfoCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @VIA\Post(
     *     path="/departments/",
     *     description="Создать подразделение",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Department\Command\CreateCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201,
     *     properties={
     *         @VIA\Property(name="id", type="integer")
     *     }
     * )
     */
    public function CreateAction(Request $request)
    {
        $uuid = $this->get('uuid.manager')->generate();
        $this->get('command_bus')->handle(new Command\CreateCommand($request->request->all(), ['uuid' => $uuid]));

        return [
            'id' => $this->get('uuid.manager')->loadId($uuid)
        ];
    }

    /**
     * @VIA\Put(
     *     path="/departments/{id}/number/",
     *     description="Переместить подразделение",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Department\Command\SetNumberCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function SetNumberAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetNumberCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @VIA\Put(
     *     path="/departments/{id}/isActive/",
     *     description="Активировать/деактивировать подразделение",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Department\Command\SetIsActiveCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function SetIsActiveAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetIsActiveCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @VIA\Get(
     *     path="/departments/{id}/warehouses/",
     *     description="Получить список складов подразделения",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Department\Query\GetWarehousesQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="OrgBundle\Bus\Department\Query\DTO\Warehouse")
     *     }
     * )
     */
    public function GetDepartmentWarehousesAction(int $id, Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetWarehousesQuery($request->query->all(), ['id' => $id]), $result);

        return $result;
    }

    /**
     * @VIA\Post(
     *     path="/departments/{id}/warehouses/",
     *     description="Добавить склад к подразделению",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Department\Command\AddWarehouseCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201,
     *     properties={
     *         @VIA\Property(name="id", type="integer")
     *     }
     * )
     */
    public function AddDepartmentWarehouseAction(int $id, Request $request)
    {
        $uuid = $this->get('uuid.manager')->generate();
        $this->get('command_bus')->handle(new Command\AddWarehouseCommand($request->request->all(), ['id' => $id, 'uuid' => $uuid]));

        return [
            'id' => $this->get('uuid.manager')->loadId($uuid)
        ];
    }

    /**
     * @VIA\Get(
     *     path="/departments/{id}/cashDesks/",
     *     description="Получить список касс подразделения",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Department\Query\GetCashDesksQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="OrgBundle\Entity\CashDesk")
     *     }
     * )
     */
    public function GetDepartmentCashDesksAction(int $id, Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetCashDesksQuery($request->query->all(), ['id' => $id]), $result);

        return $result;
    }

    /**
     * @VIA\Post(
     *     path="/departments/{id}/cashDesks/",
     *     description="Добавить кассу к подразделению",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Department\Command\AddCashDeskCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201,
     *     properties={
     *         @VIA\Property(name="id", type="integer")
     *     }
     * )
     */
    public function AddDepartmentCashDeskAction(int $id, Request $request)
    {
        $uuid = $this->get('uuid.manager')->generate();
        $this->get('command_bus')->handle(new Command\AddCashDeskCommand($request->request->all(), ['id' => $id, 'uuid' => $uuid]));

        return [
            'id' => $this->get('uuid.manager')->loadId($uuid)
        ];
    }
}
