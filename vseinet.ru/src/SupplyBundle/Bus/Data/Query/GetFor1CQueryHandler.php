<?php 

namespace SupplyBundle\Bus\Data\Query;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class GetFor1CQueryHandler extends MessageHandler
{
    /**
     * @param GetFor1CQuery $query
     *
     * @return array
     */
    public function handle(GetFor1CQuery $query) : array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        if (empty($query->ourTin) || empty($query->tin) || empty($query->supplierId) || empty($query->ourWaybillNumber) || empty($query->waybillNumber) || empty($query->date)) {
            throw new BadRequestHttpException('Укажите критерий отбора');
        }

        $where = [];
        if (!empty($query->ourTin)) {
            $where[] = 'oc.tin = '.$query->ourTin;
        }
        if (!empty($query->tin)) {
            $where[] = 'C.tin = '.$query->tin;
        }
        if (!empty($query->supplierId)) {
            $where[] = 's.supplier_id = '.$query->supplierId;
        }
        if (!empty($query->ourWaybillNumber)) {
            $where[] = 's.our_waybill_number = '.$query->ourWaybillNumber;
        }
        if (!empty($query->waybillNumber)) {
            $where[] = 's.supplier_waybill_number = '.$query->waybillNumber;
        }
        if (!empty($query->date)) {
            $where[] = 'to_char( s.registered_at, \'YYYY-mm-dd\' ) = '.$query->date;
        }

        $q = $em->createNativeQuery('
            SELECT
                s.id,
                s.created_at,
                s.supplier_id,
                C.tin,
                oc.tin AS our_tin,
                s.our_waybill_number,
                s.our_waybill_date,
                s.supplier_waybill_number AS waybill_number,
                s.supplier_waybill_date AS waybill_date,
                s.registered_at 
            FROM
                supply AS s
                JOIN counteragent AS C ON C.id = s.supplier_counteragent_id
                JOIN counteragent AS oc ON oc.id = s.our_counteragent_id 
            WHERE '.implode(' AND ', $where), new ResultSetMapping());

        return $this->camelizeKeys($q->getResult('ListAssocHydrator'));
    }
}