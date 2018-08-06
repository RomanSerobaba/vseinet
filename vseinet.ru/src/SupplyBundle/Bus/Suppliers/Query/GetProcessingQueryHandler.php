<?php 

namespace SupplyBundle\Bus\Suppliers\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use AppBundle\ORM\Query\DTORSM;

class GetProcessingQueryHandler extends MessageHandler
{
    /**
     * @param GetProcessingQuery $query
     *
     * @return array
     */
    public function handle(GetProcessingQuery $query) : array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $currentUserId = $query->isTest ? 1 : $this->get('user.identity')->getUser()->getId();

        $q = $em->createNativeQuery('
            SELECT
                s.id /*ид поставщика*/,
                s.code AS name /*наименование поставщика*/,
                s.manager_id /*ид менеджера поставщика*/,
                COUNT ( gnr.order_item_id ) :: INT AS processing_items_quantity /*количество необработанных позиций*/,
                ( SELECT id FROM supplier_reserve WHERE supplier_id = s.id AND is_shipping = FALSE AND closed_at IS NULL ) AS supplier_reserve_id 
            FROM
                get_goods_need_register_data(CURRENT_TIMESTAMP::TIMESTAMP) AS gnr
                JOIN base_product AS bp ON bp.id = gnr.base_product_id
                JOIN supplier AS s ON s.id = bp.supplier_id 
            WHERE
                s.is_active = TRUE 
                AND gnr.delta > 0 
            GROUP BY
                s.id 
            ORDER BY
                CASE WHEN s.manager_id = :user_id::INTEGER THEN 0 ELSE 1 END,
                processing_items_quantity DESC
         ', new DTORSM(\SupplyBundle\Bus\Suppliers\Query\DTO\SuppliersForOrdersProcessing::class));

        $q->setParameter('user_id', $currentUserId);

        return $q->getResult('DTOHydrator');
    }
}