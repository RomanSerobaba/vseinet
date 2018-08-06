<?php

namespace OrgBundle\Bus\Work\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityNotFoundException;
use OrgBundle\Components\SalaryComponents;

class GetSalaryCalculationQueryHandler extends MessageHandler
{
    use SalaryComponents;

    /**
     * @param GetSalaryCalculationQuery $query
     * @return array
     * @throws EntityNotFoundException
     * @throws \Doctrine\ORM\ORMException
     */
    public function handle(GetSalaryCalculationQuery $query)
    {
        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();

        $salary = $this->calculation($currentUser, $query->id, $query->date);
//        return $this->fakeResult();

        if (!$salary)
            throw new EntityNotFoundException('Сотрудник не найден');

        return $salary;
    }

    private function fakeResult() {
        return [
            'wages' => [
                [
                    'id' => 1,
                    'activeSince' => new \DateTime(date('Y-m-01 00:00:00')),
                    'activeTill' => new \DateTime(date('Y-m-d 23:59:59')),
                    'constantBase' => 2000000,
                    'planBase' => 1000000,
                    'constantSalaryAmount' => 1200000,
                    'planSalaryAmount' => 400000
                ]
            ],
            'planIndexes' => [
                [
                    'id' => 1,
                    'name' => 'test',
                    'factAmount' => 400000,
                    'planAmount' => 600000,
                    'progress' => 80,
                    'coefficient' => 20,
                    'temp' => 70
                ]
            ],
            'pieceIndexes' => [
                [
                    'id' => 1,
                    'name' => 'test',
                    'fact' => 12,
                    'rateAmount' => 4
                ]
            ],
            'fines' => [
                [
                    'id' => 1,
                    'cause' => 'test',
                    'date' => new \DateTime(date('Y-m-05 00:00:00')),
                    'category' => 'miscellaneous',
                    'amount' => 200000,
                    'createdAt' => new \DateTime(),
                    'approvedAt' => new \DateTime(),
                    'appliedAt' => new \DateTime()
                ]
            ],
            'hasTax' => true,
            'taxAmount' => 300000,
            'planProgress' => 73,
            'salaryAmount' => 1100000,
            'idealSalaryAmount' => 1200000,
            'paidAmount' => 400000,
            'hourlyRate' => 200
        ];
    }
}