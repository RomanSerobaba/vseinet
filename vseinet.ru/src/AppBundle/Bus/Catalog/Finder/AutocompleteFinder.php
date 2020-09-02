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

        $expression = $this->getQueryBuilder()->rankingExactWords($this->getQueryBuilder()->escape($this->getQueryBuilder()->escape($filter->q)));
        $snippet = $this->getQueryBuilder()->snippetWords($this->getQueryBuilder()->escape($filter->q));
        $query = "
            SELECT
                id,
                WEIGHT() AS weight,
                SNIPPET(name, '{$snippet}') AS label
            FROM category
            WHERE MATCH('{$expression}')
            ORDER BY weight DESC, rating DESC
            LIMIT ".self::COUNT_CATEGORIES."
            OPTION ranker=expr('sum(exact_hit * 5) + (1 - is_accessories) * 5 + if(average_price > 100000, 5, 0)')
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
                INNER JOIN AppBundle:CategoryStats AS cs WITH cs.categoryId = c.id
                WHERE c.aliasForId IS NULL AND cp.plevel > 0 AND cp.id IN (:ids) AND cs.countProducts > 0
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
                    $result[$id]->label = $result[$id]->name;

                    if (!empty($results['0'])) {
                        foreach ($results[0] as $elem) {
                            if ($elem['id'] == $id) {
                                $result[$id]->label = $elem['label'];
                            }
                        }
                    }
                }
            }
        }

        if (preg_match('~^[1-9]\d*$~isu', $filter->q)) {
            $product = $em->getRepository(BaseProduct::class)->find($filter->q);
            if ($product instanceof BaseProduct) {
                $p = new DTO\Autocomplete\Product($product->getCanonicalId(), $product->getName());
                $p->label = $p->name;
                $result[] = $p;
            }
        }

        $availability = $this->getUserIsEmployee() ? Availability::FOR_ALL_TIME : Availability::ACTIVE;
        $expression = $this->getQueryBuilder()->rankingExactWords($this->getQueryBuilder()->escape($this->getQueryBuilder()->escape($filter->q)));
        $snippet = $this->getQueryBuilder()->snippetWords($this->getQueryBuilder()->escape($filter->q));

        $query = "
            SELECT
                id,
                WEIGHT() AS weight,
                SNIPPET(name, '{$snippet}') AS label
            FROM product_index_{$this->getGeoCity()->getRealId()}
            WHERE MATCH('{$expression}') AND availability <= {$availability} AND is_forbidden = 0
            ORDER BY weight DESC, availability ASC, rating DESC, price ASC
            LIMIT ".self::COUNT_PRODUCTS."
            OPTION ranker=expr('sum(if(min_best_span_pos < 5, 5 - min_best_span_pos, 0) + exact_order * 15) + if(availability = 1, 14, if(availability < 4, 4 - availability, 0) * 2) + (1 - is_accessories) * 15 + if(category_average_price > 100000, 15, 0) + if(popularity > 500, 10, popularity / 50) + if(name_length < 120, 10, 0)')
            ;
        ";
        $results = $this->get('sphinx')->createQuery()->setQuery($query)->getResults();
        if (!empty($results[0])) {
            $productIds = array_map(function ($row) { return intval($row['id']); }, $results[0]);
            $products = $this->get('query_bus')->handle(new GetProductsQuery(['ids' => $productIds]));
            foreach ($products as $product) {
                $p = new DTO\Autocomplete\Product($product->id, $product->name);
                $p->label = $p->name;

                if (!empty($results['0'])) {
                    foreach ($results[0] as $elem) {
                        if ($elem['id'] == $product->id) {
                            $p->label = $elem['label'];
                        }
                    }
                }

                $result[] = $p;
            }
        }

        return $result;
    }
}
