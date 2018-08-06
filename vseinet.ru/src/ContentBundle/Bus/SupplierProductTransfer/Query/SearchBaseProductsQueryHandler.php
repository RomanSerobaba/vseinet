<?php 

namespace ContentBundle\Bus\SupplierProductTransfer\Query;

use AppBundle\Bus\Message\MessageHandler;
use ContentBundle\Entity\BaseProductImage;

class SearchBaseProductsQueryHandler extends MessageHandler
{
    public function handle(SearchBaseProductsQuery $query)
    {
        $sphinxql = $this->get('sphinxql');
        $str = $sphinxql->escapeMatch($query->q);
        $snippet = $sphinxql->escape($query->q);

        $sql = [];
        foreach (['supplier_product' => 'code', 'product' => 'name'] as $index => $column) {
            $sql[] = "
                SELECT id, WEIGHT() AS weight, SNIPPET({$column}, '{$snippet}') AS label
                FROM {$index} 
                WHERE MATCH('{$str}') AND killbill = 0
                ORDER BY weight DESC
                LIMIT {$query->limit}
                OPTION ranker=expr('sum((4*lcs+2*(min_hit_pos==1)+exact_hit)*user_weight)*1000+bm25')
            ";
        }
        $results = $sphinxql->execute(implode(';', $sql));

        $em = $this->getDoctrine()->getManager();

        $spec = new Specification\Catalog();

        $products = [];

        if (!empty($results[0])) {
            $ids = [];
            $ord = 'CASE';
            foreach (array_reverse($results[0]) as $index => $item) {
                $ids[] = $item['id'];
                $ord .= ' WHEN sp2.id = '.$item['id'].' THEN '.$index;
            }
            $ord .= ' ELSE '.($index + 1).' END';

            $q = $em->createQuery("
                SELECT 
                    NEW ContentBundle\Bus\SupplierProductTransfer\Query\DTO\FoundBaseProduct (
                        bp.id,
                        bp.name,
                        bp.categoryId,
                        p.price,
                        bpi.basename,
                        CASE 
                            WHEN {$spec->isNew()} THEN 'new'
                            WHEN {$spec->isActive()} THEN 'active'
                            WHEN {$spec->isOld()} THEN 'old'
                            WHEN {$spec->isHidden()} THEN 'hidden'
                            ELSE ''
                        END,
                        GROUP_CONCAT(DISTINCT CONCAT(s.code, ': ', COALESCE(sp.code, sp.article)) SEPARATOR ', ')
                    ),
                    {$ord} HIDDEN ORD
                FROM ContentBundle:BaseProduct bp 
                INNER JOIN PricingBundle:Product p WITH p.baseProductId = bp.id
                INNER JOIN SupplyBundle:SupplierProduct sp WITH sp.baseProductId = bp.id 
                INNER JOIN SupplyBundle:Supplier s WITH s.id = sp.supplierId 
                INNER JOIN SupplyBundle:SupplierProduct sp2 WITH sp2.baseProductId = bp.id
                LEFT OUTER JOIN ContentBundle:BaseProductImage bpi WITH bpi.baseProductId = bp.id AND bpi.sortOrder = 1
                WHERE sp2.id IN (:ids)
                GROUP BY bp.id, p.id, bpi.id, sp2.id
                ORDER BY ORD
            ");
            $q->setParameter('ids', $ids);
            $products = $q->getResult('IndexByHydrator');
        }

        if (!empty($results[1])) {
            $ids = [];
            $ord = 'CASE';
            foreach ($results[1] as $index => $item) {
                $ids[] = $item['id'];
                $ord .= ' WHEN bp.id = '.$item['id'].' THEN '.$index;
            }
            $ord .= ' ELSE '.($index + 1).' END';

            $q = $em->createQuery("
                SELECT 
                    NEW ContentBundle\Bus\SupplierProductTransfer\Query\DTO\FoundBaseProduct (
                        bp.id,
                        bp.name,
                        bp.categoryId,
                        p.price,
                        bpi.basename,
                        CASE 
                            WHEN {$spec->isNew()} THEN 'new'
                            WHEN {$spec->isActive()} THEN 'active'
                            WHEN {$spec->isOld()} THEN 'old'
                            WHEN {$spec->isHidden()} THEN 'hidden'
                            ELSE ''
                        END,
                        GROUP_CONCAT(DISTINCT CONCAT(s.code, ': ', COALESCE(sp.code, sp.article)) SEPARATOR ', ')
                    ),
                    {$ord} HIDDEN ORD
                FROM ContentBundle:BaseProduct bp 
                INNER JOIN PricingBundle:Product p WITH p.baseProductId = bp.id
                LEFT OUTER JOIN ContentBundle:BaseProductImage bpi WITH bpi.baseProductId = bp.id AND bpi.sortOrder = 1
                LEFT OUTER JOIN SupplyBundle:SupplierProduct sp WITH sp.baseProductId = bp.id 
                LEFT OUTER JOIN SupplyBundle:Supplier s WITH s.id = sp.supplierId 
                WHERE bp.id IN (:ids)
                GROUP BY bp.id, p.id, bpi.id
                ORDER BY ORD
            ");
            $q->setParameter('ids', $ids);
            $products += $q->getResult('IndexByHydrator');
            foreach ($results[1] as $item) {
                if (!empty($products[$item['id']])) {
                    $products[$item['id']]->name = $item['label'];
                }
            }
        }

        if (empty($products)) {
            return new DTO\FoundResults();
        }

        array_walk($products, function($product, $id, $regex) {
            $product->supplierCodes = preg_replace($regex, '<b>$0</b>', $product->supplierCodes);
        }, '/'.preg_quote($query->q, '/').'/ui');

        $categoryIds = [];
        foreach ($products as $product) {
            $categoryIds[] = $product->categoryId;    
        }

        $categories = [];

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\SupplierProductTransfer\Query\DTO\FoundCategory (
                    c.id,
                    c.name,
                    c.pid 
                )
            FROM ContentBundle:Category c 
            INNER JOIN ContentBundle:CategoryPath cp WITH cp.pid = c.id
            WHERE cp.id IN (:categoryIds)
            GROUP BY c.id, cp.plevel 
            ORDER BY cp.plevel
        ");
        $q->setParameter('categoryIds', $categoryIds);
        $cwb = $q->getResult('IndexByHydrator');            
        foreach ($products as $product) {
            $product->imageSrc = $em->getRepository(BaseProductImage::class)->buildSrc($this->getParameter('product.images.web.path'), $product->imageSrc, 'md');
            $category = $cwb[$product->categoryId];
            $category->products[] = $product;
            if (empty($category->breadcrumbs)) {
                $category->breadcrumbs = [];
                $pid = $category->pid;
                while ($pid) {
                    array_unshift($category->breadcrumbs, $cwb[$pid]);
                    $pid = $cwb[$pid]->pid;
                }
            }
            $category->productIds[] = $product->id;
            $categories[$product->categoryId] = $category;
        }
        
        return new DTO\FoundResults($categories, $products);
    }
}