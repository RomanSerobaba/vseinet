<?php

namespace OrgBundle\Bus\Employee\Query;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\EntityNotFoundException;
use OrgBundle\Components\EmployeeComponent;

class GetInfoQueryHandler extends MessageHandler
{
    /**
     * @param GetInfoQuery $query
     * @return DTO\EmployeeInfo
     * @throws EntityNotFoundException
     */
    public function handle(GetInfoQuery $query)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $employeeComponent = new EmployeeComponent($em);
        $employee = $employeeComponent->getInfo($query->id, true);

        if (!$employee)
            throw new EntityNotFoundException('Сотрудник не найден');

        return $employee;
    }
}