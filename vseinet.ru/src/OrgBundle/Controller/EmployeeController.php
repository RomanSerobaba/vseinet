<?php

namespace OrgBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use OrgBundle\Bus\Employee\Command;
use OrgBundle\Bus\Employee\Query;

/**
 * @VIA\Section("Структура организации - Сотрудники")
 */
class EmployeeController extends RestController
{
    
    /**
     * @VIA\Get(
     *     path="/org/employees/",
     *     description="Получить нормализованное дерево сотрудников с выбором по части фио",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Employee\Query\GetQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="OrgBundle\Bus\Employee\Query\DTO\Employees")
     *     }
     * )
     */
    public function GetFlatAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetQuery($request->query->all()), $items);

        return $items;
    }

    /**
     * @VIA\Get(
     *     path="/org/employees/tree/",
     *     description="Получить дерево сотрудников",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Employee\Query\GetTreeQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="OrgBundle\Bus\Employee\Query\DTO\DepartmentTree")
     *     }
     * )
     */
    public function GetTreeAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetTreeQuery($request->query->all()), $items);

        return $items;
    }

    /**
     * @VIA\Post(
     *     path="/employees/",
     *     description="Создать сотрудника в структуре подразделений",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Employee\Command\CreateCommand")
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
     * @VIA\Get(
     *     path="/employees/{id}/",
     *     description="Получить информацию о сотруднике",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Employee\Query\GetInfoQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="OrgBundle\Bus\Employee\Query\DTO\EmployeeInfo")
     *     }
     * )
     */
    public function GetEmployeeInfoAction(int $id, Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetInfoQuery($request->query->all(), ['id' => $id]), $employee);

        return $employee;
    }

    /**
     * @VIA\Put(
     *     path="/employees/{id}/number/",
     *     description="Переместить сотрудника в структуре подразделений",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Employee\Command\SetNumberCommand")
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
     *     path="/employees/{id}/firedAt/",
     *     description="Уволить сотрудника",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Employee\Command\SetFiredAtCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function SetFiredAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetFiredAtCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @VIA\Get(
     *     path="/employees/{id}/relatives/",
     *     description="Получить информацию о родственниках сотрудника",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Employee\Query\GetRelativesQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="OrgBundle\Bus\Employee\Query\DTO\Relative")
     *     }
     * )
     */
    public function GetEmployeeRelativesAction(int $id, Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetRelativesQuery($request->query->all(), ['id' => $id]), $relatives);

        return $relatives;
    }

    /**
     * @VIA\Put(
     *     path="/employees/{id}/relatives/",
     *     description="Редактировать информацию о родственниках сотрудника",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Employee\Command\UpdateRelativesCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function UpdateEmployeeRelativesAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\UpdateRelativesCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @VIA\Get(
     *     path="/employees/{id}/documents/",
     *     description="Получить информацию о документах сотрудника",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Employee\Query\GetDocumentsQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="OrgBundle\Bus\Employee\Query\DTO\Document")
     *     }
     * )
     */
    public function GetEmployeeDocumentsAction(int $id, Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetDocumentsQuery($request->query->all(), ['id' => $id]), $documents);

        return $documents;
    }

    /**
     * @VIA\Put(
     *     path="/employees/{id}/documents/",
     *     description="Редактировать информацию о документах сотрудника",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Employee\Command\UpdateDocumentsCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function UpdateEmployeeDocumentsAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\UpdateDocumentsCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @VIA\Put(
     *     path="/employees/{id}/workInfo/",
     *     description="Редактировать рабочую информацию сотрудника",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Employee\Command\UpdateWorkInfoCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function UpdateEmployeeWorkInfoAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\UpdateWorkInfoCommand($request->request->all(), ['id' => $id]));
    }
}
