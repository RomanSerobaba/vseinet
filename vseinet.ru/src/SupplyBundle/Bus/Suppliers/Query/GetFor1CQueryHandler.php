<?php 

namespace SupplyBundle\Bus\Suppliers\Query;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\Query\ResultSetMapping;
use SupplyBundle\Entity\ViewSupply;
use SupplyBundle\Entity\SupplierOrder;

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

        $q = $em->createNativeQuery('
            SELECT 
                id,
                code,
                is_active 
            FROM
                supplier
        ', new ResultSetMapping());

        return $this->camelizeKeys($q->getResult('ListAssocHydrator'));
    }
}