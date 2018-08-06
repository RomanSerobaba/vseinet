<?php

namespace OrgBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use OrgBundle\Bus\DepartmentType\Command;
use OrgBundle\Bus\DepartmentType\Query;

/**
 * @VIA\Section("Структура организации - Типы подразделений")
 */
class DepartmentTypeController extends RestController
{
    /**
     * @VIA\Get(
     *     path="/departmentsTypes/",
     *     description="Получить список типов подразделений",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\DepartmentType\Query\GetDepartmentTypesQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="OrgBundle\Entity\DepartmentType")
     *     }
     * )
     */
    public function GetDepartmentTypesAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetDepartmentTypesQuery($request->query->all()), $items);

        return $items;
    }

    /**
     * @VIA\Get(
     *     path="/activitiesAreas/",
     *     description="Получить список фильтров выборки показателей",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\DepartmentType\Query\GetActivitiesAreasQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="OrgBundle\Entity\ActivityArea")
     *     }
     * )
     */
    public function GetActivitiesAreasAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetActivitiesAreasQuery($request->query->all()), $items);

        return $items;
    }

    /**
     * @VIA\Get(
     *     path="/activitiesIndexes/",
     *     description="Получить список разрезов измерений показателей",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\DepartmentType\Query\GetActivitiesIndexesQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="OrgBundle\Entity\ActivityIndex")
     *     }
     * )
     */
    public function GetActivitiesIndexesAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetActivitiesIndexesQuery($request->query->all()), $items);

        return $items;
    }

    /**
     * @VIA\Get(
     *     path="/activitiesObjects/",
     *     description="Получить список объектов измерений показателей",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\DepartmentType\Query\GetActivitiesObjectsQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="OrgBundle\Bus\DepartmentType\Query\DTO\ActivityObject")
     *     }
     * )
     */
    public function GetActivitiesObjectsAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetActivitiesObjectsQuery($request->query->all()), $items);

        return $items;
    }

    /**
     * @VIA\Get(
     *     path="/departmentsTypes/{id}/activities/",
     *     description="Получить список показателей сотрудника, работающего в заданном типе подразделения",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\DepartmentType\Query\GetDepartmentTypeActivitiesQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="OrgBundle\Bus\DepartmentType\Query\DTO\DepartmentTypeActivity")
     *     }
     * )
     */
    public function GetDepartmentTypeActivitiesAction(int $id, Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetDepartmentTypeActivitiesQuery($request->query->all(), ['id' => $id]), $items);

        return $items;
    }

    /**
     * @VIA\Post(
     *     path="/departmentsTypes/{departmentTypeId}/activities/",
     *     description="Добавить показатель для сотрудника заданного типа подразделения",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\DepartmentType\Command\CreateDepartmentTypeActivityCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201,
     *     properties={
     *         @VIA\Property(name="id", type="integer")
     *     }
     * )
     */
    public function CreateDepartmentTypeActivityAction(int $departmentTypeId, Request $request)
    {
        $uuid = $this->get('uuid.manager')->generate();
        $this->get('command_bus')->handle(new Command\CreateDepartmentTypeActivityCommand($request->request->all(),
            ['departmentTypeId' => $departmentTypeId, 'uuid' => $uuid]));

        return [
            'id' => $this->get('uuid.manager')->loadId($uuid)
        ];
    }

    /**
     * @VIA\Put(
     *     path="/departmentsTypes/{departmentTypeId}/activities/",
     *     description="Редактировать показатель для сотрудника заданного типа подразделения",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\DepartmentType\Command\UpdateDepartmentTypeActivityCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function UpdateDepartmentTypeActivityAction(int $departmentTypeId, Request $request)
    {
        $this->get('command_bus')->handle(new Command\UpdateDepartmentTypeActivityCommand($request->request->all(),
            ['departmentTypeId' => $departmentTypeId]));
    }

    /**
     * @VIA\Delete(
     *     path="/departmentsTypes/{departmentTypeId}/activities/",
     *     description="Удалить показатель для сотрудника заданного типа подразделения",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\DepartmentType\Command\DeleteDepartmentTypeActivityCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function DeleteDepartmentTypeActivityAction(int $departmentTypeId, Request $request)
    {
        $this->get('command_bus')->handle(new Command\DeleteDepartmentTypeActivityCommand($request->request->all(),
            ['departmentTypeId' => $departmentTypeId]));
    }
}