<?php 

namespace ShopBundle\Bus\Cart\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use AppBundle\Enum\GoodsConditionCode;
use AppBundle\ORM\Query\DTORSM;
use Doctrine\ORM\Query\ResultSetMapping;

class GetListQueryHandler extends MessageHandler
{
    /**
     *
     */
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

        $discountCode = $this->get('session')->get('discountCode');

        $data = [
            'count' => 0,
            'discountCode' => $discountCode,
            'discount' => 0,
            'total' => 0,
            'totalDiscount' => 0,
            'delivery2City' => 0,
            'rise' => 0,
            'products' => [],
        ];

        $cart = [];
        if (empty($currentUser)) {
            $cart = $this->get('session')->get('cart');
        } else {
            $q = $em->createNativeQuery('
                SELECT
                    id,
                    user_id,
                    product_id,
                    quantity,
                    change_type_id
                FROM
                    cart 
                WHERE
                    user_id = :id 
            ', new ResultSetMapping());
            $q->setParameter('id', $id);

            $rows =  $q->getResult('ListAssocHydrator');
            foreach ($rows as $row) {
                $cart[$row['product_id']] = $row;
            }
        }

        if (!empty($cart)) {
            $ids = array_keys($cart);

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
                c.quantity,
                c.change_type_id
            FROM 
                cart c
                INNER JOIN product p ON p.id = c.product_id
                INNER JOIN base_product bp ON p.base_product_id = bp.id
                LEFT OUTER JOIN brand b ON b.id = bp.brand_id
                LEFT JOIN get_goods_reserve_register_data(CURRENT_TIMESTAMP::TIMESTAMP, bp.id , null, :free) as grr ON 1=1
                LEFT join geo_room as gr on gr.id = grr.geo_room_id 
                LEFT join geo_point as gp on gp.id = gr.geo_point_id
                LEFT join representative as r on r.geo_point_id = gp.id and r.has_retail = true and r.is_active = true
            WHERE 
                c.product_id IN (:ids)
            ', new DTORSM(\ShopBundle\Bus\Cart\Query\DTO\CartProduct::class));

            $q->setParameter('free', GoodsConditionCode::FREE);
            $q->setParameter('ids', $ids);

            $products = $q->getResult('DTOHydrator');

            foreach ($products as $product) {
                $id = $product->id;
                $product->quantity = $cart[$id]['quantity'];
                $product->changeTypeId = $cart[$id]['change_type_id'];

                $data['products'][$id] = $product;
                $data['count'] += $product->quantity;
                $data['total'] += $product->price * $product->quantity;
                $data['discount'] += 0 * $product->quantity;
                $data['totalDiscount'] += 0 * $product->quantity;
                $data['delivery2City'] += 0 * $product->quantity;
                $data['rise'] += 0 * $product->quantity;
            }
        }

        return $data;
    }
}