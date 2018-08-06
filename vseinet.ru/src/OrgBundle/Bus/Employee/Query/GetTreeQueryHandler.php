<?php 

namespace OrgBundle\Bus\Employee\Query;

use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Bus\Message\MessageHandler;

class GetTreeQueryHandler extends MessageHandler
{
    public function handle(GetTreeQuery $query)
    {
        
        $tTree = "
            WITH

                department_with_path as(

                    select distinct
                        odp.org_department_id,
                        od.name,
                        array(select pid from org_department_path where org_department_id = odp.org_department_id order by plevel) as org_department_path
                    from
                        org_department_path odp
                    left join org_department od on
                        od.id = odp.org_department_id
                    order by array(select pid from org_department_path where org_department_id = odp.org_department_id order by plevel)
                )

            SELECT

                out_table.org_department_id,
                out_table.name as org_department_name,
                out_table.user_id,
                out_table.fio as user_name,
                out_table.org_department_path

            from (

            SELECT

                dwp_full.org_department_id,
                dwp_full.name,
                null as user_id,
                '' as fio,
                dwp_full.org_department_path

            FROM

                department_with_path dwp_full

            union

            SELECT
                dwp.org_department_id,
                dwp.name,
                eh.org_employee_user_id as user_id,
                concat(
                    case when pp.firstname is null  then '' else concat(pp.firstname, ' ') end,
                    case when pp.secondname is null then '' else concat(pp.secondname, ' ') end,
                    case when pp.lastname is null   then '' else pp.lastname end) as fio,
                dwp.org_department_path

            FROM

                department_with_path dwp

                left join org_employee_to_department ed ON
                    dwp.org_department_id = ed.org_department_id

                left join org_employment_history eh on
                    eh.org_employee_user_id = ed.org_employee_user_id

                left join person pp ON
                    eh.org_employee_user_id = pp.id

            WHERE
                eh.fired_at IS NULL
            ) out_table
            order by
                out_table.org_department_path,
                out_table.fio,
                out_table.user_id
        ";

        $rTree = new ResultSetMapping();
        $rTree->addScalarResult('org_department_id', 'orgDepartmentId', 'integer');
        $rTree->addScalarResult('org_department_name', 'orgDepartmentName', 'string');
        $rTree->addScalarResult('user_id', 'userId', 'integer');
        $rTree->addScalarResult('user_name', 'userName', 'string');
        $rTree->addScalarResult('org_department_path', 'orgDepartmentPath', 'string');

        $em = $this->getDoctrine()->getManager();
        $qTree = $em->createNativeQuery($tTree, $rTree);

        $results = $qTree->getArrayResult();

        $arrayOfDepartment = [];
        $currentDepartmentPath = '---';
        foreach ($results as $value) {
            $value['orgDepartmentPathAsArray'] = explode(',', substr($value['orgDepartmentPath'], 1 ,-1));
            
            if ($currentDepartmentPath != $value['orgDepartmentPath']) {
                if (!isset($arrayOfDepartment[$value['orgDepartmentPath']])) {
                    $arrayOfDepartment[$value['orgDepartmentPath']] = new DTO\DepartmentTree();
                    $arrayOfDepartment[$value['orgDepartmentPath']]->id = $value['orgDepartmentId'];
                    $arrayOfDepartment[$value['orgDepartmentPath']]->name =  $value['orgDepartmentName'];
                    // Найти родителя
                    if (1 < count($value['orgDepartmentPathAsArray'])) {
                        $arrayOfDepartment['{'. implode(',', array_slice($value['orgDepartmentPathAsArray'], 0, -1)) .'}']->departments[] = 
                                &$arrayOfDepartment[$value['orgDepartmentPath']];
                    }
                    $currentDepartmentPath = $value['orgDepartmentPath'];
                }
            }
            
            // Обработка сотрудников
            
            if (!empty($value['userId'])) {
                //var_dump($arrayOfDepartment[$value['orgDepartmentPath']]); die();
                $arrayOfDepartment[$value['orgDepartmentPath']]->employees[] = new DTO\EmployeeTree($value['userId'], $value['userName']);
            }
        }
        
        if (0 == count($arrayOfDepartment)) {
           throw new NotFoundHttpException('Структура организации не обнаружена');
        }
        
        return array_shift($arrayOfDepartment);
    }

}