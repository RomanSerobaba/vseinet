<?php

namespace OrgBundle\Bus\Department\Query;

use AppBundle\Bus\Message\MessageHandler;
use OrgBundle\Entity\CashDesk;

class GetCashDesksQueryHandler extends MessageHandler
{
    public function handle(GetCashDesksQuery $query)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /** @var CashDesk[] $cashDesks */
        $cashDesks = $em->createQuery('
                SELECT
                    c
                FROM OrgBundle:CashDesk AS c
                WHERE c.departmentId = :departmentId
            ')
            ->setParameter('departmentId', $query->id)
            ->getResult();

        return $cashDesks;
    }
}