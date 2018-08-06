<?php 

namespace MatrixBundle\Bus\Representative\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\ORM\Query\DTORSM;
use Doctrine\ORM\Query\ResultSetMapping;
use MatrixBundle\Entity\TradeMatrixTemplate;
use OrgBundle\Entity\Representative;
use AppBundle\Enum\GoodsConditionCode;
use AppBundle\Enum\OrderTypeCode;

class GetMatrixQueryHandler extends MessageHandler
{
    public function handle(GetMatrixQuery $query)
    {
        $representative = $this->getDoctrine()->getManager()->getRepository(Representative::class)->findOneBy(['geoPointId' => $query->id]);
        if (!$representative instanceof Representative) {
            throw new NotFoundHttpException(sprintf('Точка %d не найдена', $query->id));
        }

        if ($query->templateId > 0) {
            $template = $this->getDoctrine()->getManager()->getRepository(TradeMatrixTemplate::class)->find($query->templateId);
            if (!$template instanceof TradeMatrixTemplate) {
                throw new NotFoundHttpException(sprintf('Шаблон %d не найден', $query->templateId));
            }
        }

        if (!$query->templateId) {
            $subquery = "
                SELECT
                    oi.base_product_id,
                    SUM ( sr.delta ) AS sold_quantity,
                    0 AS reserve_quantity,
                    0 AS matrix_quantity,
                    0 AS transit_quantity,
                    0 AS ordered_quantity 
                FROM
                    sales_register AS sr
                    JOIN order_item AS oi ON oi.ID = sr.order_item_id
                    JOIN \"order\" AS o ON o.ID = oi.order_id
                    JOIN base_product AS bp ON bp.ID = oi.base_product_id 
                WHERE
                    o.geo_point_id = :representativeId 
                    AND sr.registered_at > now() - INTERVAL '12 month'
                    AND o.type_code = :type_code_shop 
                GROUP BY
                    oi.base_product_id 
                HAVING
                    SUM ( sr.delta ) > 0 

                UNION ALL

                SELECT
                    grr.base_product_id,
                    0 AS sold_quantity,
                    SUM ( grr.delta ) AS reserve_quantity,
                    0 AS matrix_quantity,
                    0 AS transit_quantity,
                    0 AS ordered_quantity 
                FROM
                    goods_reserve_register_current AS grr
                    JOIN geo_room AS gr ON gr.ID = grr.geo_room_id 
                WHERE
                    gr.geo_point_id = :representativeId 
                    AND grr.goods_condition_code = :goods_condition_code_free 
                GROUP BY
                    grr.base_product_id 
                HAVING
                    SUM ( grr.delta ) > 0 
                    
                UNION ALL

                SELECT
                    grr.base_product_id,
                    0 AS sold_quantity,
                    0 AS reserve_quantity,
                    0 AS matrix_quantity,
                    SUM ( grr.delta ) AS transit_quantity,
                    0 AS ordered_quantity 
                FROM
                    goods_reserve_register_current AS grr
                    JOIN order_item AS oi ON oi.ID = grr.order_item_id
                    JOIN \"order\" AS o ON o.ID = oi.order_id 
                WHERE
                    o.geo_point_id = :representativeId 
                    AND o.type_code = :type_code_resupply 
                GROUP BY
                    grr.base_product_id 
                HAVING
                    SUM ( grr.delta ) > 0 

                UNION ALL

                SELECT
                    gnr.base_product_id,
                    0 AS sold_quantity,
                    0 AS reserve_quantity,
                    0 AS matrix_quantity,
                    0 AS transit_quantity,
                    SUM ( gnr.delta ) AS ordered_quantity 
                FROM
                    goods_need_register AS gnr
                    JOIN order_item AS oi ON oi.ID = gnr.order_item_id
                    JOIN \"order\" AS o ON o.ID = oi.order_id 
                WHERE
                    o.geo_point_id = :representativeId 
                    AND o.type_code = :type_code_resupply
                GROUP BY
                    gnr.base_product_id 
                HAVING
                    SUM ( gnr.delta ) > 0 

                UNION ALL

                SELECT
                    srr.base_product_id,
                    0 AS sold_quantity,
                    0 AS reserve_quantity,
                    0 AS matrix_quantity,
                    0 AS transit_quantity,
                    SUM ( srr.delta ) AS ordered_quantity 
                FROM
                    supplier_reserve_register AS srr
                    JOIN order_item AS oi ON oi.ID = srr.order_item_id
                    JOIN \"order\" AS o ON o.ID = oi.order_id 
                WHERE
                    o.geo_point_id = :representativeId 
                    AND o.type_code = :type_code_resupply 
                GROUP BY
                    srr.base_product_id 
                HAVING
                    SUM ( srr.delta ) > 0 

                UNION ALL

                SELECT
                    mp.base_product_id,
                    0 AS sold_quantity,
                    0 AS reserve_quantity,
                    mp.quantity AS matrix_quantity,
                    0 AS transit_quantity,
                    0 AS ordered_quantity 
                FROM
                    trade_matrix_product_to_representative AS mp 
                WHERE
                    mp.representative_id = :representativeId 
                    AND mp.trade_matrix_template_id IS NULL
                    AND mp.quantity > 0
            ";
                
            $em = $this->getDoctrine()->getManager();
            $rsm = new ResultSetMapping();
            $rsm->addScalarResult('total', 'total', 'integer');
            $q = $em->createNativeQuery("
                SELECT 
                    COUNT(DISTINCT p.base_product_id) AS total
                FROM
                    ({$subquery}) AS P
                    ", $rsm);
            $q->setParameter('representativeId', $query->id);
            $q->setParameter('type_code_shop', OrderTypeCode::SHOP);
            $q->setParameter('type_code_resupply', OrderTypeCode::RESUPPLY);
            $q->setParameter('goods_condition_code_free', GoodsConditionCode::FREE);

            $total = $q->getSingleScalarResult();
            
            $q = $em->createNativeQuery("
                SELECT 
                    bp.id,
                    bp.name,
                    SUM(P.sold_quantity) AS sold_quantity,
                    SUM(P.reserve_quantity) AS reserve_quantity,
                    SUM(P.matrix_quantity) AS matrix_quantity,
                    SUM(P.transit_quantity) AS transit_quantity,
                    SUM(P.ordered_quantity) AS ordered_quantity,
                    bp.category_id,
                    bp.supplier_price AS purchase_price
                FROM
                    ({$subquery}) AS P 
                    JOIN base_product AS bp ON bp.ID = P.base_product_id
                    JOIN category AS c ON bp.category_id = c.id
                    JOIN category_path AS cp ON cp.id = c.id AND cp.plevel = 1
                    JOIN category AS pc ON cp.pid = pc.id
                    GROUP BY pc.id, c.id, bp.id
                    ORDER BY pc.name, c.name, bp.name
                    LIMIT {$query->limit} 
                    OFFSET " . (($query->page - 1) * $query->limit) . "
                    ", new DTORSM(\MatrixBundle\Bus\Representative\Query\DTO\BaseProduct::class));

            $q->setParameter('representativeId', $query->id);
            $q->setParameter('type_code_shop', OrderTypeCode::SHOP);
            $q->setParameter('type_code_resupply', OrderTypeCode::RESUPPLY);
            $q->setParameter('goods_condition_code_free', GoodsConditionCode::FREE);

            $products = $q->getResult('DTOHydrator');
        } else {            
            $em = $this->getDoctrine()->getManager();
            $rsm = new ResultSetMapping();
            $rsm->addScalarResult('total', 'total', 'integer');
            $q = $em->createNativeQuery("
                SELECT 
                    COUNT(DISTINCT mp.base_product_id) AS total
                FROM
                    trade_matrix_template_product AS mp
                    LEFT JOIN trade_matrix_product_to_representative AS mpr ON mp.base_product_id = mpr.base_product_id AND mp.trade_matrix_template_id = mpr.trade_matrix_template_id AND mpr.representative_id = :representativeId
                WHERE
                    mp.trade_matrix_template_id = :templateId
                    AND COALESCE(mpr.quantity, mp.quantity) > 0
                    ", $rsm);                
            $q->setParameter('representativeId', $query->id);
            $q->setParameter('templateId', $query->templateId);

            $total = $q->getSingleScalarResult();

            $q = $em->createNativeQuery("
                        SELECT
                        bp.id,
                        bp.name,
                        (
                            SELECT SUM
                                ( sr.delta ) 
                            FROM
                                sales_register AS sr
                                JOIN order_item AS oi ON oi.ID = sr.order_item_id
                                JOIN \"order\" AS o ON o.ID = oi.order_id
                                JOIN base_product AS bp ON bp.ID = oi.base_product_id 
                            WHERE
                                o.geo_point_id = :representativeId 
                                AND sr.registered_at > now() - INTERVAL '12 month' 
                                AND o.type_code = :type_code_shop
                                AND oi.base_product_id = mp.base_product_id 
                        ) AS sold_quantity,
                        (
                            SELECT SUM
                                ( grr.delta ) 
                            FROM
                                goods_reserve_register_current AS grr
                                JOIN geo_room AS gr ON gr.ID = grr.geo_room_id 
                            WHERE
                                gr.geo_point_id = :representativeId 
                                AND grr.goods_condition_code = :goods_condition_code_free
                                AND grr.base_product_id = mp.base_product_id 
                        ) AS reserve_quantity,
                        COALESCE(mpr.quantity, mp.quantity) AS matrix_quantity,
                        (
                            SELECT SUM
                                ( grr.delta ) 
                            FROM
                                goods_reserve_register_current AS grr
                                JOIN order_item AS oi ON oi.ID = grr.order_item_id
                                JOIN \"order\" AS o ON o.ID = oi.order_id 
                            WHERE
                                o.geo_point_id = :representativeId 
                                AND o.type_code = :type_code_resupply
                                AND grr.base_product_id = mp.base_product_id 
                        ) AS transit_quantity,
                        COALESCE ((
                            SELECT SUM
                                ( gnr.delta ) 
                            FROM
                                goods_need_register AS gnr
                                JOIN order_item AS oi ON oi.ID = gnr.order_item_id
                                JOIN \"order\" AS o ON o.ID = oi.order_id 
                            WHERE
                                o.geo_point_id = :representativeId 
                                AND o.type_code = :type_code_resupply
                                AND gnr.base_product_id = mp.base_product_id 
                                ),
                            0 
                            ) + COALESCE ((
                            SELECT SUM
                                ( srr.delta ) 
                            FROM
                                supplier_reserve_register AS srr
                                JOIN order_item AS oi ON oi.ID = srr.order_item_id
                                JOIN \"order\" AS o ON o.ID = oi.order_id 
                            WHERE
                                o.geo_point_id = :representativeId 
                                AND o.type_code = :type_code_resupply
                                AND srr.base_product_id = mp.base_product_id 
                            ),
                            0 
                        ) AS ordered_quantity,
                        bp.category_id,
                        bp.supplier_price AS purchase_price
                    FROM
                        trade_matrix_template_product AS mp
                        LEFT JOIN trade_matrix_product_to_representative AS mpr ON mp.base_product_id = mpr.base_product_id AND mp.trade_matrix_template_id = mpr.trade_matrix_template_id AND mpr.representative_id = :representativeId
                        JOIN base_product AS bp ON bp.ID = mp.base_product_id
                        JOIN category AS c ON bp.category_id = c.id
                        JOIN category_path AS cp ON cp.id = c.id AND cp.plevel = 1
                        JOIN category AS pc ON cp.pid = pc.id
                    WHERE
                        mp.trade_matrix_template_id = :templateId
                        AND COALESCE(mpr.quantity, mp.quantity) > 0
                    ORDER BY pc.name, c.name, bp.name
                    LIMIT {$query->limit} 
                    OFFSET " . (($query->page - 1) * $query->limit) . "
                    ", new DTORSM(\MatrixBundle\Bus\Representative\Query\DTO\BaseProduct::class));

            $q->setParameter('representativeId', $query->id);
            $q->setParameter('templateId', $query->templateId);
            $q->setParameter('type_code_shop', OrderTypeCode::SHOP);
            $q->setParameter('type_code_resupply', OrderTypeCode::RESUPPLY);
            $q->setParameter('goods_condition_code_free', GoodsConditionCode::FREE);

            $products = $q->getResult('DTOHydrator');
        }

        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT 
                NEW MatrixBundle\Bus\Representative\Query\DTO\Category (
                    c.id,
                    c.name,
                    CASE WHEN cp.id = cp.pid AND mcr.quantity IS NULL THEN 0 ELSE mcr.quantity END,
                    CASE WHEN cp.plevel > 1 THEN pcp.pid ELSE c.pid END
                )
                FROM ContentBundle:BaseProduct AS bp
                JOIN ContentBundle:CategoryPath AS cp WITH bp.categoryId = cp.id
                JOIN ContentBundle:Category AS c WITH cp.pid = c.id
                LEFT JOIN ContentBundle:CategoryPath AS pcp WITH pcp.id = c.id AND pcp.plevel = 1
                LEFT JOIN MatrixBundle:TradeMatrixCategoryToRepresentative AS mcr WITH mcr.categoryId = bp.categoryId AND mcr.representativeId = :representativeId
                WHERE bp.id IN (:productsIds) AND (cp.id = cp.pid OR cp.plevel <= 1)
        ");
        $q->setParameter('productsIds', array_column($products, 'id'));              
        $q->setParameter('representativeId', $query->id);
        $categories = $q->getResult('IndexByHydrator');

        foreach ($categories as $category) {
            if (null !== $category->pid) {
                $categories[$category->pid]->childrenIds[] = $category->id;
            }
        }

        foreach ($products as $product) {
            $categories[$product->categoryId]->productsIds[] = $product->id;
        }

        if (!$query->templateId) {
            $q = $em->createNativeQuery("
                SELECT
                    c.id,
                    COALESCE((
                        SELECT SUM
                            ( sr.delta ) 
                        FROM
                            sales_register AS sr
                            JOIN order_item AS oi ON oi.ID = sr.order_item_id
                            JOIN \"order\" AS o ON o.ID = oi.order_id
                            JOIN base_product AS bp ON bp.ID = oi.base_product_id 
                        WHERE
                            o.geo_point_id = :representativeId 
                            AND sr.registered_at > now() - INTERVAL '12 month' 
                            AND o.type_code = :type_code_shop
                            AND bp.category_id = c.id 
                    ), 0) AS sold_quantity,
                    COALESCE((
                        SELECT SUM
                            ( grr.delta ) 
                        FROM
                            goods_reserve_register_current AS grr
                            JOIN geo_room AS gr ON gr.ID = grr.geo_room_id 
                            JOIN base_product AS bp ON bp.id = grr.base_product_id
                        WHERE
                            gr.geo_point_id = :representativeId 
                            AND grr.goods_condition_code = :goods_condition_code_free
                            AND bp.category_id = c.id 
                    ), 0) AS reserve_quantity,
                    COALESCE((
                        SELECT SUM
                            ( grr.delta ) 
                        FROM
                            goods_reserve_register_current AS grr
                            JOIN order_item AS oi ON oi.ID = grr.order_item_id
                            JOIN \"order\" AS o ON o.ID = oi.order_id 
                            JOIN base_product AS bp ON bp.id = grr.base_product_id
                        WHERE
                            o.geo_point_id = :representativeId 
                            AND o.type_code = :type_code_resupply
                            AND bp.category_id = c.id 
                    ), 0) AS transit_quantity,
                    COALESCE ((
                        SELECT SUM
                            ( gnr.delta ) 
                        FROM
                            goods_need_register AS gnr
                            JOIN order_item AS oi ON oi.ID = gnr.order_item_id
                            JOIN \"order\" AS o ON o.ID = oi.order_id 
                            JOIN base_product AS bp ON bp.id = gnr.base_product_id
                        WHERE
                            o.geo_point_id = :representativeId 
                            AND o.type_code = :type_code_resupply
                            AND bp.category_id = c.id 
                            ),
                        0 
                        ) + COALESCE ((
                        SELECT SUM
                            ( srr.delta ) 
                        FROM
                            supplier_reserve_register AS srr
                            JOIN order_item AS oi ON oi.ID = srr.order_item_id
                            JOIN \"order\" AS o ON o.ID = oi.order_id 
                            JOIN base_product AS bp ON bp.id = srr.base_product_id
                        WHERE
                            o.geo_point_id = :representativeId 
                            AND o.type_code = :type_code_resupply
                            AND bp.category_id = c.id 
                        ),
                        0 
                    ) AS ordered_quantity
                FROM category AS c
                WHERE c.id IN (:categoriesIds) AND c.pid > 0
            ", new ResultSetMapping());      
            $q->setParameter('categoriesIds', array_keys($categories));             
            $q->setParameter('representativeId', $query->id);
            $q->setParameter('type_code_shop', OrderTypeCode::SHOP);
            $q->setParameter('type_code_resupply', OrderTypeCode::RESUPPLY);
            $q->setParameter('goods_condition_code_free', GoodsConditionCode::FREE);
            $categoriesQuantities = $q->getResult('IndexByNativeHydrator');
            
            foreach ($categoriesQuantities as $categoryQuantity) {
                $categories[$categoryQuantity['id']]->reserveQuantity += $categoryQuantity['reserve_quantity'];
                $categories[$categoryQuantity['id']]->transitQuantity += $categoryQuantity['transit_quantity'];
                $categories[$categoryQuantity['id']]->orderedQuantity += $categoryQuantity['ordered_quantity'];
                $categories[$categoryQuantity['id']]->soldQuantity += $categoryQuantity['sold_quantity'];
            }
        }

        return new DTO\Matrix($categories, $products, $total);
    }
}
