<?php 

namespace SupplyBundle\Bus\Suppliers\Query;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\ResultSetMapping;


class GetCounteragentsQueryHandler extends MessageHandler
{
    /**
     * @param GetCounteragentsQuery $query
     *
     * @return array
     */
    public function handle(GetCounteragentsQuery $query) : array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $q = $em->createNativeQuery('
            SELECT 
                C.tin,
                C.name,
                stc.is_active 
            FROM
                supplier_to_counteragent AS stc
                JOIN counteragent AS C ON C.id = stc.counteragent_id 
            WHERE
                stc.supplier_id = :supplier_id
        ', new ResultSetMapping());
        $q->setParameter('supplier_id', $query->id);

        return $this->camelizeKeys($q->getResult('ListAssocHydrator'));
    }
}