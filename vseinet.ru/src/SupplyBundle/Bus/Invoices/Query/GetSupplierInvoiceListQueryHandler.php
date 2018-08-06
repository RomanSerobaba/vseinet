<?php 

namespace SupplyBundle\Bus\Invoices\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\DocumentTypeCode;
use AppBundle\Specification\ViewSupplierProductSpecification;
use Doctrine\ORM\Query\ResultSetMapping;
use SupplyBundle\Component\ShipmentComponent;


class GetSupplierInvoiceListQueryHandler extends MessageHandler
{
    /**
     * @param GetSupplierInvoiceListQuery $query
     *
     * @return array
     */
    public function handle(GetSupplierInvoiceListQuery $query) : array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $spec = new ViewSupplierProductSpecification();

        // Список товаров 
        $q = $em->createNativeQuery("
            SELECT
                bp.id /*ид товара*/,
                round( SUM( gnr.quantity * gnr.purchase_price ) / SUM( gnr.quantity ) ) AS purchase_price /*цена закупки*/,
                sp.code /*код товара у поставщика*/,
                COALESCE ( sp.name, bp.name ) as name /*наименование товара у поставщика*/,
                bpi.basename AS photo_url /*урл картинки*/,
                SUM( gnr.quantity ) AS quantity /*требуемое к отгрузке количество*/
            FROM (
                SELECT
                    SUM( gnr.delta ) AS quantity,
                    gnr.base_product_id,
                    bp.supplier_price AS purchase_price 
                FROM
                    goods_need_register AS gnr
                    JOIN base_product AS bp ON bp.id = gnr.base_product_id
                    JOIN supplier_invoice AS si ON si.supplier_id = bp.supplier_id
                    JOIN order_item AS oi ON oi.id = gnr.order_item_id
                    JOIN \"order\" AS o ON o.id = oi.order_id 
                WHERE
                    si.id = :invoice_id 
                    AND si.destination_point_id IN ( o.geo_point_id, :point_id ) 
                GROUP BY
                    gnr.base_product_id,
                    bp.supplier_price 
                HAVING
                    SUM( gnr.delta ) > 0 
                
                UNION ALL
                
                SELECT
                    SUM( - gnr.delta ) AS quantity,
                    gnr.base_product_id,
                    gnr.purchase_price 
                FROM
                    supplier_invoice AS si
                    JOIN supplier_reserve AS sr ON si.supplier_id = sr.supplier_id 
                        AND sr.is_shipping = FALSE 
                        AND sr.closed_at IS NULL 
                    LEFT JOIN supplier_reserve AS srr ON srr.supplier_id = sr.supplier_id 
                        AND srr.is_shipping = TRUE 
                        AND srr.closed_at IS NULL 
                    JOIN goods_need_register AS gnr ON gnr.supplier_reserve_id = COALESCE ( srr.id, sr.id )
                    JOIN order_item AS oi ON oi.id = gnr.order_item_id
                    JOIN \"order\" AS o ON o.id = oi.order_id 
                WHERE
                    si.id = :invoice_id 
                    AND gnr.supplier_invoice_id IS NULL 
                    AND si.destination_point_id IN ( o.geo_point_id, :point_id ) 
                GROUP BY
                    gnr.base_product_id,
                    gnr.purchase_price 
                HAVING
                    SUM( - gnr.delta ) > 0 
                ) AS gnr
                JOIN base_product AS bp ON gnr.base_product_id = bp.id
                LEFT JOIN base_product_image AS bpi ON bpi.base_product_id = bp.id AND bpi.sort_order = 1
                ".$spec->buildLeftJoin('bp.id', 'bp.supplier_id')."
            WHERE ".$spec->buildWhere(false)."
            GROUP BY
                bp.id,
                sp.code,
                sp.name,
                bpi.basename 
            ORDER BY
                bp.name
        ", new ResultSetMapping());

        $q->setParameter('supplier_invoice_id', $query->id);
        $q->setParameter('point_id', $this->getParameter('default.point.id'));

        $products = $q->getResult('ListAssocHydrator');

        $component = new ShipmentComponent($em);

        return $this->camelizeKeys(['products' => $products, 'orders' => $component->getSupplierInvoiceListOrders($query->id),], ['purchase_price',]);
    }
}