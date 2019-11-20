<?php

namespace AppBundle\Bus\Catalog\Finder;

use AppBundle\Bus\Catalog\Query\GetProductsQuery;
use AppBundle\Entity\BaseProduct;
use AppBundle\Bus\Catalog\Enum\Availability;

class AutocompleteFinder extends AbstractProductFinder
{
    public const COUNT_CATEGORIES = 3;
    public const COUNT_PRODUCTS = 10;

    /**
     * @param iterable $values
     *
     * @return self
     */
    public function setFilterData(iterable $values): self
    {
        $this->getFilter()->parse($values);

        return $this;
    }

    public function getResult()
    {
        $filter = $this->getFilter();
        $em = $this->getDoctrine()->getManager();
        $result = [];

        $query = "
            SELECT
                id,
                WEIGHT() AS weight
            FROM category
            WHERE MATCH('".$this->getQueryBuilder()->rankingExactWords($this->getQueryBuilder()->escape($this->getQueryBuilder()->escape($filter->q)))."')
            ORDER BY weight DESC, rating DESC
            LIMIT ".self::COUNT_CATEGORIES."
            OPTION ranker=expr('sum(sum_idf * 100 + exact_hit * 5) + if(average_price > 500000, 2, IF(average_price > 80000, 1, 0)) * 5')
        ";
        $results = $this->get('sphinx')->createQuery()->setQuery($query)->getResults();
        if (!empty($results[0])) {
            $categoryIds = array_map(function ($row) { return intval($row['id']); }, $results[0]);
            $q = $em->createQuery("
                SELECT
                    NEW AppBundle\Bus\Catalog\Finder\DTO\Autocomplete\Category (
                        c.id,
                        c.name,
                        c.pid
                    )
                FROM AppBundle:Category c
                INNER JOIN AppBundle:CategoryPath cp WITH cp.pid = c.id
                WHERE c.aliasForId IS NULL AND cp.plevel > 0 AND cp.id IN (:ids)
            ");
            $q->setParameter('ids', $categoryIds);
            $categories = $q->getResult('IndexByHydrator');

            foreach ($categoryIds as $id) {
                if (!empty($categories[$id])) { // кастыль
                    $pid = $categories[$id]->pid;
                    $breadcrumbs = [];
                    while ($pid) {
                        $breadcrumbs[] = $categories[$pid];
                        $pid = $categories[$pid]->pid;
                    }
                    $result[$id] = clone $categories[$id];
                    $result[$id]->breadcrumbs = array_reverse($breadcrumbs);
                }
            }
        }

        if (is_numeric($filter->q)) {
            $product = $em->getRepository(BaseProduct::class)->find($filter->q);
            if ($product instanceof BaseProduct) {
                $result[] = new DTO\Autocomplete\Product($product->getCanonicalId(), $product->getName());
            }
        }

        $availability = $this->getUserIsEmployee() ? Availability::FOR_ALL_TIME : Availability::ACTIVE;

        $query = "
            SELECT
                id,
                WEIGHT() AS weight
            FROM product_index_{$this->getGeoCity()->getRealId()}
            WHERE MATCH('".$this->getQueryBuilder()->rankingExactWords($this->getQueryBuilder()->escape($this->getQueryBuilder()->escape($filter->q)))."') AND availability <= {$availability}
            ORDER BY weight DESC, availability ASC, rating DESC
            LIMIT ".self::COUNT_PRODUCTS."
            OPTION ranker=expr('sum(sum_idf * 10 + if(min_best_span_pos < 5, 5, 0)) + if(availability < 4, 4 - availability, 0) * 4 + if(category_average_price > 500000, 2, if(category_average_price > 80000, 1, 0)) * 10 + if(popularity > 50, 10, 0)')
            ;
        ";
        $results = $this->get('sphinx')->createQuery()->setQuery($query)->getResults();
        if (!empty($results[0])) {
            $productIds = array_map(function ($row) { return intval($row['id']); }, $results[0]);
            $products = $this->get('query_bus')->handle(new GetProductsQuery(['ids' => $productIds]));
            foreach ($products as $product) {
                $result[] = new DTO\Autocomplete\Product($product->id, $product->name);
            }
        }

        return $result;
    }
}
