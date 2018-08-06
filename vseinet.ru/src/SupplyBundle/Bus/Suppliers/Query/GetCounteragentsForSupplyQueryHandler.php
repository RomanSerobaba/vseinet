<?php 

namespace SupplyBundle\Bus\Suppliers\Query;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\ResultSetMapping;
use AppBundle\ORM\Query\DTORSM;

class GetCounteragentsForSupplyQueryHandler extends MessageHandler
{
    public function handle(GetCounteragentsForSupplyQuery $query) : array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery('
            SELECT
                NEW SupplyBundle\Bus\Suppliers\Query\DTO\CounteragentsForSupply (
                    c.id,
                    c.name
                )
            FROM
                SupplyBundle:Supply AS s
                JOIN SupplyBundle:SupplierToCounteragent AS stc WITH s.supplierId = stc.supplierId
                JOIN AccountingBundle:Counteragent AS c WITH c.id = stc.counteragentId
            WHERE
                s.id = :supply_id 
                AND ( s.supplierCounteragentId = stc.counteragentId OR stc.isActive = TRUE ) 
            ORDER BY
                c.name
        ');

        $q->setParameter('supply_id', $query->supplyId);

        return $q->getResult();
    }
}