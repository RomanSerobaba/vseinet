<?php 

namespace SupplyBundle\Bus\Suppliers\Query;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\ResultSetMapping;


class GetCounteragentQueryHandler extends MessageHandler
{
    /**
     * @param GetCounteragentQuery $query
     *
     * @return array
     */
    public function handle(GetCounteragentQuery $query) : array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $q = $em->createNativeQuery('
            SELECT 
                c.*,
                stc.is_main
            FROM
                counteragent c 
                INNER JOIN supplier_to_counteragent stc ON stc.counteragent_id = c.id
            WHERE
                stc.supplier_id = :supplier_id AND c.id = :counteragent_id
        ', new ResultSetMapping());
        $q->setParameter('supplier_id', $query->id);
        $q->setParameter('counteragent_id', $query->cid);

        $rows = $q->getResult('ListAssocHydrator');
        $row = array_shift($rows);

        return $this->camelizeKeys($row);
    }
}