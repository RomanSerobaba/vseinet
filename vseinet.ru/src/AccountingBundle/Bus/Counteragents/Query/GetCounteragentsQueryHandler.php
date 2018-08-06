<?php

namespace AccountingBundle\Bus\Counteragents\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\ContactType;
use AppBundle\Entity\Role;
use OrderBundle\Entity\OrderItemStatus;
use Doctrine\ORM\Query\ResultSetMapping;


class GetCounteragentsQueryHandler extends MessageHandler
{
    /**
     * @param GetCounteragentsQuery $query
     *
     * @return array
     */
    public function handle(GetCounteragentsQuery $query): array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $where = '';
        if (!empty($query->tin)) {
            $where = " WHERE tin = '".$query->tin."'";
        }

        $q = $em->createNativeQuery('
            SELECT
                tin,
                name 
            FROM
                counteragent' . $where, new ResultSetMapping());

        return $this->camelizeKeys($q->getResult('ListAssocHydrator'));
    }
}