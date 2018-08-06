<?php 

namespace SupplyBundle\Bus\Orders\Query;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\Query\ResultSetMapping;

class GetManagersQueryHandler extends MessageHandler
{
    /**
     * @param GetManagersQuery $query
     *
     * @return array
     */
    public function handle(GetManagersQuery $query) : array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $q = $em->createNativeQuery('
            SELECT
                vup.id,
                vup.fullname,
                org_department.name,
                org_department.pid 
            FROM
                org_employee
                JOIN org_employment_history ON org_employment_history.org_employee_user_id = org_employee.user_id
                JOIN func_view_user_person(org_employee.user_id) AS vup ON vup.id = org_employee.user_id
                JOIN org_department ON org_department.id = org_employee.org_department_id 
            WHERE
                org_employment_history.fired_at IS NULL
        ', new ResultSetMapping());

        return $this->camelizeKeys($q->getResult('ListAssocHydrator'));
    }
}