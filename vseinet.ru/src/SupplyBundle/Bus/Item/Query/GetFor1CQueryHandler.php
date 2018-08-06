<?php 

namespace SupplyBundle\Bus\Item\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\DocumentTypeCode;
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

        $q = $em->createNativeQuery('
            SELECT
                si.id,
                si.base_product_id,
                si.purchase_price,
                si.quantity,
                bp.name 
            FROM
                supply AS s
                JOIN supply_item AS si ON si.parent_doc_type = :supply 
                    AND si.parent_doc_id = s.id 
                JOIN base_product AS bp ON bp.id = si.base_product_id 
            WHERE
                s.id = :supply_id
        ', new ResultSetMapping());

        $q->setParameter('supply', DocumentTypeCode::SUPPLY);
        $q->setParameter('supply_id', $query->id);

        return $this->camelizeKeys($q->getResult('ListAssocHydrator'), ['purchase_price',]);
    }
}