<?php 

namespace SupplyBundle\Bus\Orders\Query;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\Query\ResultSetMapping;

class GetSuppliersQueryHandler extends MessageHandler
{
    /**
     * @param GetSuppliersQuery $query
     *
     * @return array
     */
    public function handle(GetSuppliersQuery $query) : array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $q = $em->createNativeQuery('
            SELECT
                s.id,
                s.code as name,
                vup.fullname as manager 
            FROM
                supplier AS s
                LEFT JOIN func_view_user_person(s.manager_id) AS vup ON vup.id = s.manager_id 
            WHERE
                s.is_active = TRUE 
            ORDER BY
	            s.code
        ', new ResultSetMapping());

        return $this->camelizeKeys($q->getResult('ListAssocHydrator'));
    }
}