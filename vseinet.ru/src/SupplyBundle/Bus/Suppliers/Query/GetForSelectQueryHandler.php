<?php 

namespace SupplyBundle\Bus\Suppliers\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\ORM\Query\DTORSM;

class GetForSelectQueryHandler extends MessageHandler
{
    public function handle(GetForSelectQuery $query) : array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery('
            SELECT
                NEW SupplyBundle\Bus\Suppliers\Query\DTO\SuppliersForSelect (
                    s.id,
                    s.code
                )
            FROM
                SupplyBundle:Supplier s
            WHERE
                s.isActive = TRUE 
            ORDER BY
                s.name
        ');

        return $q->getResult();
    }
}