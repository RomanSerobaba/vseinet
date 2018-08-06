<?php 

namespace ShopBundle\Bus\Favorite\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use AppBundle\Enum\GoodsConditionCode;
use AppBundle\ORM\Query\DTORSM;
use Doctrine\ORM\Query\ResultSetMapping;

class GetListQueryHandler extends MessageHandler
{
    public function handle(GetListQuery $query) : array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();
        $id = $currentUser->getId();

        $q = $em->createNativeQuery('
            SELECT
                product_id
            FROM
                favorite 
            WHERE
                created_by = :id 
            ORDER BY
                created_at
        ', new ResultSetMapping());
        $q->setParameter('id', $id);

        $ids = [];
        $rows =  $q->getResult('ListAssocHydrator');
        foreach ($rows as $row) {
            $ids[] = $row['product_id'];
        }

        if (!$ids) {
            return [];
        }

        $q = $em->createNativeQuery('
            SELECT
                p.id,
                p.price,
                b.logo as brand_logo,
                b.id as brand_id,
                bp.id as base_product_id,
                bp.name,
                gp.id as geo_point_id, 
                gp.name as geo_point_name, 
                sum(grr.delta) as geo_point_quantity
            FROM product p
            INNER JOIN base_product bp ON p.base_product_id = bp.id
            LEFT OUTER JOIN brand b ON b.id = bp.brand_id
            LEFT JOIN favorite f ON f.product_id = p.id AND f.created_by = :id
            LEFT JOIN get_goods_reserve_register_data(CURRENT_TIMESTAMP::TIMESTAMP, bp.id , null, :free) as grr ON 1=1
            LEFT join geo_room as gr on gr.id = grr.geo_room_id 
            LEFT join geo_point as gp on gp.id = gr.geo_point_id
            LEFT join representative as r on r.geo_point_id = gp.id and r.has_retail = true and r.is_active = true
            WHERE p.id IN (:ids)
            GROUP BY gp.id, p.id, b.id, bp.id
            HAVING SUM(grr.delta) > 0 OR sum(grr.delta) IS NULL
        ', new DTORSM(\ShopBundle\Bus\Favorite\Query\DTO\Favorites::class));
        $q->setParameter('free', GoodsConditionCode::FREE);
        $q->setParameter('id', $id);
        $q->setParameter('ids', $ids);

        return $q->getResult('DTOHydrator');
    }
}