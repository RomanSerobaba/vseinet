<?php 

namespace OrgBundle\Bus\Employee\Query;

use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Bus\Message\MessageHandler;
use AppBundle\ORM\Query\DTORSM;

class GetQueryHandler extends MessageHandler
{
    public function handle(GetQuery $query)
    {
        
        $queryText = "
            select
                emp.user_id as id,
                (ehi.fired_at is not null) as is_fired,
                concat(
                    case when per.firstname is null  then '' else concat(per.firstname, 
                        case when per.secondname is null  then '' else ' ' end) end,
                    case when per.secondname is null then '' else concat(per.secondname,
                        case when per.lastname is null  then '' else ' ' end) end,
                    case when per.lastname is null   then '' else per.lastname end) as name
            from org_employee emp
            inner join person per ON
                per.id = emp.user_id
            inner join org_employment_history ehi
                on ehi.org_employee_user_id = emp.user_id
            ";
        
        $queryWhere = [];

        if (empty($query->withFired)) {
            $queryWhere[] = "ehi.fired_at is null";
        }
        
        if (!empty($query->q)) {
            $q = str_replace("'", "''", mb_strtoupper($query->q));
            $queryWhere[] = "(
                upper(per.firstname) like '%{$q}%' or
                upper(per.secondname) like '%{$q}%' or
                upper(per.lastname) like '%{$q}%')";
        }
        
        if (!empty($queryWhere)) {
            $queryText .= "
                where
                ". implode(" and ", $queryWhere);
        }
        
        $em = $this->getDoctrine()->getManager();
        
        $dbQuery = $em->createNativeQuery($queryText, new DTORSM(DTO\Employee::class, DTORSM::ARRAY_INDEX));
        $employees = $dbQuery->getResult('DTOHydrator');
        
        if (empty($employees)) {
            
            $employees = NULL;
            $departments = NULL;
                    
        }else{
            
            $employeesIds = [];
            foreach ($employees as $value) {
                $employeesIds[] = $value->id;
            }
            $employeesIdsStr = implode(",", $employeesIds);
            unset($employeesIds);
            
            $queryText = "
                with
                    all_dep as(
                        select distinct
                            pid as did,
                            plevel as level
                        from org_department_path
                        where
                            org_department_id in (
                                select distinct
                                    org_department_id
                                from org_employee where user_id in ({$employeesIdsStr})
                            )
                    )

                select
                    out.id,
                    out.name,
                    array_to_json(out.department_ids) as departments_ids,
                    array_to_json(out.employees_ids) as employees_ids
                from (
                    select distinct
--                        ad.level,
                        ad.did as id,
                        dep.name,
                        array(
                            select org_department_id from org_department_path
                            where
                            pid = ad.did and
                            level - ad.level = 1
                            and org_department_id in (select did from all_dep)
                        ) as department_ids,
                        array(
                            select user_id from org_employee
                            where user_id in ({$employeesIdsStr}) and 
                            org_department_id = ad.did
                        ) as employees_ids
                        from all_dep ad
                        left join org_department dep on dep.id = ad.did
--                        order by ad.level, ad.did
                ) out";

            $dbQuery = $em->createNativeQuery($queryText, new DTORSM(DTO\Department::class, DTORSM::ARRAY_INDEX));
            $departments = $dbQuery->getResult('DTOHydrator');
        }
        
        return new DTO\Employees($employees, $departments);
    }

}