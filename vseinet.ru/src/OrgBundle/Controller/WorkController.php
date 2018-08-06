<?php

namespace OrgBundle\Controller;

use AppBundle\Annotation as VIA;
use AppBundle\Controller\RestController;
use OrgBundle\Bus\Work\Command;
use OrgBundle\Bus\Work\Query;
use Symfony\Component\HttpFoundation\Request;

/**
 * @VIA\Section("Структура организации - Рабочий процесс")
 */
class WorkController extends RestController
{
    /**
     * @VIA\Get(
     *     path="/orgStructure/forSalary/",
     *     description="Получить нормализованное дерево подразделений и сотрудников, с начисленными зарплатами",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Work\Query\GetOrgStructureForSalaryQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", name="departments", model="OrgBundle\Bus\Department\Query\DTO\Department"),
     *         @VIA\Property(type="array", name="employees",   model="OrgBundle\Bus\Work\Query\DTO\Employee")
     *     }
     * )
     * @param Request $request
     * @return mixed
     */
    public function GetOrgStructureForSalaryAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetOrgStructureForSalaryQuery($request->query->all()), $items);

        return $items;
    }

    /**
     * @VIA\Get(
     *     path="/orgStructure/forPlan/",
     *     description="Получить нормализованное дерево подразделений для планов",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Work\Query\GetOrgStructureForPlanResultsQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", name="departments", model="OrgBundle\Bus\Department\Query\DTO\Department"),
     *         @VIA\Property(type="array", name="employees",   model="OrgBundle\Bus\Employee\Query\DTO\EmployeeInfo")
     *     }
     * )
     * @param Request $request
     * @return mixed
     */
    public function GetOrgStructureForPlanResultsAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetOrgStructureForPlanResultsQuery($request->query->all()), $items);

        return $items;
    }

    /**
     * @VIA\Get(
     *     path="/planResults/",
     *     description="Получить список планов и результатов",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Work\Query\GetPlanResultsQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="OrgBundle\Bus\Work\Query\DTO\MonthPlans")
     *     }
     * )
     * @param Request $request
     * @return mixed
     */
    public function GetPlanResultsAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetPlanResultsQuery($request->query->all()), $items);

        return $items;
    }

    /**
     * @VIA\Put(
     *     path="/work/start/",
     *     description="Начать отсчет рабочего времени",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Work\Command\StartWorkCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     * @param Request $request
     */
    public function StartWorkAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\StartWorkCommand($request->request->all()));
    }

    /**
     * @VIA\Put(
     *     path="/work/stop/",
     *     description="Остановить отсчет рабочего времени",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Work\Command\StopWorkCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     * @param Request $request
     */
    public function StopWorkAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\StopWorkCommand($request->request->all()));
    }

    /**
     * @VIA\Get(
     *     path="/employees/{id}/schedules/",
     *     description="Получить список расписаний рабочего времени",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Work\Query\GetSchedulesQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="OrgBundle\Entity\EmployeeSchedule")
     *     }
     * )
     * @param int $id
     * @param Request $request
     * @return mixed
     */
    public function GetSchedulesAction(int $id, Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetSchedulesQuery($request->query->all(), ['id' => $id]), $schedules);

        return $schedules;
    }

    /**
     * @VIA\Post(
     *     path="/employees/{id}/schedules/",
     *     description="Добавить рабочее расписание",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Work\Command\AddEmployeeScheduleCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201,
     *     properties={
     *         @VIA\Property(name="id", type="integer")
     *     }
     * )
     * @param int $id
     * @param Request $request
     * @return array
     */
    public function AddEmployeeScheduleAction(int $id, Request $request)
    {
        $uuid = $this->get('uuid.manager')->generate();
        $this->get('command_bus')->handle(new Command\AddEmployeeScheduleCommand($request->request->all(), ['id' => $id, 'uuid' => $uuid]));

        return [
            'id' => $this->get('uuid.manager')->loadId($uuid)
        ];
    }

    /**
     * @VIA\Get(
     *     path="/employees/{id}/attendance/",
     *     description="Отчет о рабочем времени за период",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Work\Query\GetAttendanceQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="OrgBundle\Bus\Work\Query\DTO\Attendance")
     *     }
     * )
     * @param int $id
     * @param Request $request
     * @return mixed
     */
    public function GetAttendanceAction(int $id, Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetAttendanceQuery($request->query->all(), ['id' => $id]), $attendance);

        return $attendance;
    }

    /**
     * @VIA\Get(
     *     path="/employees/{id}/attendanceRequests/",
     *     description="Отчет о запросах на корректировку рабочего времени за период",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Work\Query\GetAttendanceRequestsQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="OrgBundle\Bus\Work\Query\DTO\AttendanceRequest")
     *     }
     * )
     * @param int $id
     * @param Request $request
     * @return mixed
     */
    public function GetAttendanceRequestsAction(int $id, Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetAttendanceRequestsQuery($request->query->all(), ['id' => $id]), $aRequests);

        return $aRequests;
    }

    /**
     * @VIA\Post(
     *     path="/employees/{id}/attendanceRequests/",
     *     description="Создать запрос на корректировку рабочего времени по дате",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Work\Command\AddAttendanceRequestCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201,
     *     properties={
     *         @VIA\Property(name="id", type="integer")
     *     }
     * )
     * @param int $id
     * @param Request $request
     * @return array
     */
    public function AddAttendanceRequestsAction(int $id, Request $request)
    {
        $uuid = $this->get('uuid.manager')->generate();
        $this->get('command_bus')->handle(new Command\AddAttendanceRequestCommand($request->request->all(), ['id' => $id, 'uuid' => $uuid]));

        return [
            'id' => $this->get('uuid.manager')->loadId($uuid)
        ];
    }

    /**
     * @VIA\Get(
     *     path="/employees/{id}/salaryCalculation/",
     *     description="Пересчитать зарплату",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Work\Query\GetSalaryCalculationQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array")
     *     }
     * )
     * @param int $id
     * @param Request $request
     * @return mixed
     */
    public function GetSalaryCalculationAction(int $id, Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetSalaryCalculationQuery($request->query->all(), ['id' => $id]), $salary);

        return $salary;
    }

    /**
     * @VIA\Post(
     *     path="/employees/{id}/wages/",
     *     description="Редактировать ставку",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Work\Command\AddEmployeeWageCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201,
     *     properties={
     *         @VIA\Property(name="id", type="integer")
     *     }
     * )
     * @param int $id
     * @param Request $request
     * @return array
     */
    public function AddEmployeeWageAction(int $id, Request $request)
    {
        $uuid = $this->get('uuid.manager')->generate();
        $this->get('command_bus')->handle(new Command\AddEmployeeWageCommand($request->request->all(), ['id' => $id, 'uuid' => $uuid]));

        return [
            'id' => $this->get('uuid.manager')->loadId($uuid)
        ];
    }

    /**
     * @VIA\Post(
     *     path="/employees/{id}/tax/",
     *     description="Включить начисление налога",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Work\Command\AddEmployeeTaxCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201,
     *     properties={
     *         @VIA\Property(name="id", type="integer")
     *     }
     * )
     * @param int $id
     * @param Request $request
     * @return array
     */
    public function AddEmployeeTaxAction(int $id, Request $request)
    {
        $uuid = $this->get('uuid.manager')->generate();
        $this->get('command_bus')->handle(new Command\AddEmployeeTaxCommand($request->request->all(), ['id' => $id, 'uuid' => $uuid]));

        return [
            'id' => $this->get('uuid.manager')->loadId($uuid)
        ];
    }

    /**
     * @VIA\Delete(
     *     path="/employees/{id}/tax/",
     *     description="Отключить начисление налога",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Work\Command\DeleteEmployeeTaxCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     * @param int $id
     * @param Request $request
     */
    public function DeleteEmployeeTaxAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\DeleteEmployeeTaxCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @VIA\Put(
     *     path="/employees/{id}/tax/",
     *     description="Установить сумму налога",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Work\Command\SaveEmployeeTaxValueCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     * @param int $id
     * @param Request $request
     */
    public function SaveEmployeeTaxValueAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SaveEmployeeTaxValueCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @VIA\Put(
     *     path="/employees/{id}/paydayAmount/",
     *     description="Добавить сумму к очереди выдачи зарплат",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Work\Command\SaveEmployeePaydayAmountCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     * @param int $id
     * @param Request $request
     */
    public function SaveEmployeePaydayAmountAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SaveEmployeePaydayAmountCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @VIA\Delete(
     *     path="/employees/{id}/paydayAmount/",
     *     description="Убрать сумму из очереди выдачи зарплат",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Work\Command\DeleteEmployeePaydayAmountCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     * @param int $id
     * @param Request $request
     */
    public function DeleteEmployeePaydayAmountAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\DeleteEmployeePaydayAmountCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @VIA\Get(
     *     path="/paymentQueueItems/",
     *     description="Получить список сумм в очереди выдачи зарплат ",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Work\Query\GetPaymentQueueItemsQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="OrgBundle\Bus\Work\Query\DTO\PaymentQueueItem")
     *     }
     * )
     * @param Request $request
     * @return mixed
     */
    public function GetPaymentQueueItemsAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetPaymentQueueItemsQuery($request->query->all()), $salary);

        return $salary;
    }

    /**
     * @VIA\Post(
     *     path="/expenses/forSalary/",
     *     description="Выдать зарплаты указанным сотрудникам",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Work\Command\ExpensesForSalaryCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     * @param Request $request
     */
    public function ExpensesForSalaryAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\ExpensesForSalaryCommand($request->request->all()));
    }

    /**
     * @VIA\Post(
     *     path="/employees/{id}/fines/",
     *     description="Создать премию/депремирование",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Work\Command\AddEmployeeFineCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201,
     *     properties={
     *         @VIA\Property(name="id", type="integer")
     *     }
     * )
     * @param int $id
     * @param Request $request
     * @return array
     */
    public function AddEmployeeFineAction(int $id, Request $request)
    {
        $uuid = $this->get('uuid.manager')->generate();
        $this->get('command_bus')->handle(new Command\AddEmployeeFineCommand($request->request->all(), ['id' => $id, 'uuid' => $uuid]));

        return [
            'id' => $this->get('uuid.manager')->loadId($uuid)
        ];
    }

    /**
     * @deprecated Использовать вместо него "/employeeFines/{id}/isApproved/"
     * @VIA\Put(
     *     path="/attendanceRequests/{id}/isApproved/",
     *     description="Одобрить корректировку рабочего времени",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Work\Command\SetEmployeeFineIsApprovedCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     * @param int $id
     * @param Request $request
     */
    public function SetAttendanceRequestIsApprovedAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetEmployeeFineIsApprovedCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @deprecated Использовать вместо него "/employeeFines/{id}/isApplied/"
     * @VIA\Put(
     *     path="/attendanceRequests/{id}/isApplied/",
     *     description="Применить корректировку рабочего времени",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Work\Command\SetEmployeeFineIsAppliedCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     * @param int $id
     * @param Request $request
     */
    public function SetAttendanceRequestIsAppliedAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetEmployeeFineIsAppliedCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @deprecated Использовать вместо него "/employeeFines/{id}/isDeclined/"
     * @VIA\Put(
     *     path="/attendanceRequests/{id}/isDeclined/",
     *     description="Отказать корректировку рабочего времени",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Work\Command\SetEmployeeFineIsDeclinedCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     * @param int $id
     * @param Request $request
     */
    public function SetAttendanceRequestIsDeclinedAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetEmployeeFineIsDeclinedCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @VIA\Put(
     *     path="/employeeFines/{id}/isApproved/",
     *     description="Одобрить (де)премирование",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Work\Command\SetEmployeeFineIsApprovedCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     * @param int $id
     * @param Request $request
     */
    public function SetEmployeeFineIsApprovedAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetEmployeeFineIsApprovedCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @VIA\Put(
     *     path="/employeeFines/{id}/isApplied/",
     *     description="Применить (де)премирование",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Work\Command\SetEmployeeFineIsAppliedCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     * @param int $id
     * @param Request $request
     */
    public function SetEmployeeFineIsAppliedAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetEmployeeFineIsAppliedCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @VIA\Put(
     *     path="/employeeFines/{id}/isDeclined/",
     *     description="Отказать (де)премирование",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Work\Command\SetEmployeeFineIsDeclinedCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     * @param int $id
     * @param Request $request
     */
    public function SetEmployeeFineIsDeclinedAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetEmployeeFineIsDeclinedCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @VIA\Put(
     *     path="/activityHistory/{activityId}/plan/",
     *     description="Установить месячный плановый показатель",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Work\Command\SetActivityHistoryPlanByDateCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     * @param int $activityId
     * @param Request $request
     */
    public function SetActivityHistoryPlanByDateAction(int $activityId, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetActivityHistoryPlanByDateCommand($request->request->all(), ['activityId' => $activityId]));
    }
}
