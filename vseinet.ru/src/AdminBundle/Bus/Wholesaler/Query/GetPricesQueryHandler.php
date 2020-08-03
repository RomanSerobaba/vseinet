<?php

namespace AdminBundle\Bus\Wholesaler\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Doctrine\ORM\Query\DTORSM;
use AppBundle\Entity\BaseProduct;
use AppBundle\Enum\ProductAvailabilityCode;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetPricesQueryHandler extends MessageHandler
{
    public function handle(GetPricesQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository(BaseProduct::class)->find($query->baseProductId);
        if (!$product instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар с кодом %d не найден', $query->baseProductId));
        }

        $q = $em->createNativeQuery('
                with sp as (
                    select
                        sp.id,
                        wc.id as wholesaler_contract_id,
                        wc.name as wholesaler_contract_name,
                        bp.canonical_id as base_product_id,
                        ceil((100 + case when wc2s.trade_margin is null then wc.default_margin else wc2s.trade_margin end) / 100 * sp.initial_price / 100) * 100 as purchase_price
                    from base_product as bp
                    inner join supplier_product as sp on sp.base_product_id = bp.id
                    inner join base_product as bp2 on bp2.canonical_id = bp.canonical_id
                    inner join wholesale_contract as wc on wc.terminated_at is null
                    left outer join wholesale_contract_to_supplier as wc2s on wc2s.supplier_id = sp.supplier_id and wc2s.wholesale_contract_id = wc.id and wc2s.is_active = true
                    inner join supplier as s on s.id = sp.supplier_id
                    where sp.initial_price > 0 and s.is_supplier = true and bp.canonical_id = :base_product_id and sp.product_availability_code = :productAvailabilityCode_AVAILABLE
                ),
                supplier_products as (
                    select base_product_id, wholesaler_contract_id, purchase_price, wholesaler_contract_name, rank() over (partition by base_product_id, wholesaler_contract_id, wholesaler_contract_name order by purchase_price, id) as ord from sp
                )
                select
                    sp.wholesaler_contract_id as id,
                    sp.wholesaler_contract_name as name,
                    sp.purchase_price as price
                from base_product as bp
                inner join supplier_products as sp on sp.base_product_id = bp.id and sp.ord = 1
                where bp.canonical_id = :base_product_id
                group by
                    sp.wholesaler_contract_id,
                    sp.wholesaler_contract_name,
                    sp.purchase_price
                order by sp.wholesaler_contract_name
        ', new DTORSM(DTO\Price::class));
        $q->setParameter('base_product_id', $query->baseProductId);
        $q->setParameter('productAvailabilityCode_AVAILABLE', ProductAvailabilityCode::AVAILABLE);
        $remains = $q->getResult('DTOHydrator');

        return $remains;
    }
}
