<?php

namespace AppBundle\Bus\Catalog\Finder;

use AppBundle\Container\ContainerAware;
use AppBundle\Bus\Catalog\Enum\Availability;
use AppBundle\Bus\Catalog\Enum\Nofilled;

class AbstractProductFinder extends ContainerAware
{
    public const COUNT_TOP_BRANDS = 7;

    /**
     * @return Filter
     */
    public function getFilter(): Filter
    {
        return $this->get('catalog.product.finder.filter');
    }

    /**
     * @return QueryBuilder
     */
    public function getQueryBuilder(): QueryBuilder
    {
        return $this->get('catalog.product.finder.query_builder')->reset();
    }

    /**
     * @param array $filter
     */
    public function handleRequest(array $filter = [])
    {
        $this->getFilter()->handleRequest($filter);
    }

    /**
     * @param array $found
     *
     * @return array
     */
    public function getCategoryId2Count(array $found): array
    {
        $categoryId2count = [];
        foreach ($found as $row) {
            $categoryId2count[$row['category_id']] = intval($row['count(*)']);
        }

        return $categoryId2count;
    }

    /**
     * @param array $found
     *
     * @return array
     */
    protected function getCategories(array $found): DTO\Categories
    {
        if (empty($found)) {
            return [];
        }

        $categoryId2count = $this->getCategoryId2Count($found);

        $q = $this->getDoctrine()->getManager()->createQuery('
            SELECT
                NEW AppBundle\Bus\Catalog\Finder\DTO\Category (
                    c.id,
                    c.name,
                    p.name,
                    c2.id,
                    c2.name,
                    c1.id,
                    c1.name
                ),
                CASE WHEN cs.isAccessories = false THEN 1 ELSE 2 END AS HIDDEN ORD
            FROM AppBundle:Category AS c
            INNER JOIN AppBundle:Category AS p WITH p.id = c.pid
            INNER JOIN AppBundle:CategoryStats AS cs WITH cs.categoryId = c.id
            INNER JOIN AppBundle:CategoryPath AS cp2 WITH cp2.id = c.id AND cp2.plevel = 2
            INNER JOIN AppBundle:Category AS c2 WITH c2.id = cp2.pid
            INNER JOIN AppBundle:Category AS c1 WITH c1.id = c2.pid
            WHERE c.id IN (:ids) AND c.id != 7562 AND c.aliasForId IS NULL
            ORDER BY cs.popularity DESC, ORD, c.name
        ');
        $q->setParameter('ids', array_keys($categoryId2count));
        $categories = $q->getResult('IndexByHydrator');
        if (count($categories) <= 1) {
            return new DTO\Categories();
        }

        foreach ($categories as $id => $category) {
            $category->countProducts = $categoryId2count[$id];
        }

        $groups = [];
        foreach ($categories as $category) {
            if ($category->id === $category->id2 || isset($groups[$category->id1])) {
                if (!isset($groups[$category->id1])) {
                    $groups[$category->id1] = ['categories' => [], 'frequency' => 0];
                }
                $groups[$category->id1]['categories'][] = $category;
                $groups[$category->id1]['frequency'] += $category->countProducts;
            } else {
                if (!isset($groups[$category->id2])) {
                    $groups[$category->id2] = ['categories' => [], 'frequency' => 0];
                }
                $groups[$category->id2]['categories'][] = $category;
                $groups[$category->id2]['frequency'] += $category->countProducts;
            }
        }

        usort($groups, function ($g1, $g2) {
            return $g1['frequency'] < $g2['frequency'];
        });

        $categories = array_reduce($groups, function ($carry, $group) {
            return array_merge($carry, $group['categories']);
        }, []);

        $main = array_slice($categories, 0, 3, true);
        if (count($main)) {
            $filter = $this->getFilter();
            foreach ($main as $index => $category) {
                $category->isActive = !empty($filter->categoryIds[$category->id]);
                $category->url = '?'.http_build_query($filter->build(['c' => $category->id]));
                foreach ($main as $index2 => $category2) {
                    if ($index !== $index2 && $category->name === $category2->name) {
                        $category->name = $category->parentName.' / '.$category->name;
                        $category2->name = $category2->parentName.' / '.$category2->name;
                    }
                }
            }
            $all = new DTO\Category(0, 'Все');
            $all->isActive = empty($filter->categoryIds);
            $all->url = '?'.http_build_query($filter->build(['c' => null]));
            array_unshift($main, $all);
        }
        $categories = array_slice($categories, 3, count($categories), true);
        $tree = [];
        foreach ($categories as $category) {
            if (!isset($tree[$category->id2])) {
                if (5 === count($tree)) {
                    break;
                }
                $tree[$category->id2] = new DTO\Category($category->id2, $category->name2);
            }
            $tree[$category->id2]->children[] = $category;
        }
        $tree = array_values($tree);
        $total = 15;
        foreach ($tree as $index => $category) {
            $total -= count($category->children);
            if ($total <= 0) {
                break;
            }
        }
        $tree = array_slice($tree, 0, $index + 1);

        return new DTO\Categories($main, $tree);
    }

    /**
     * @param array $found
     *
     * @return array
     */
    public function getBrandId2Count(array $found): array
    {
        $brandId2count = [];
        foreach ($found as $row) {
            $brandId2count[$row['brand_id']] = intval($row['count(*)']);
        }

        return $brandId2count;
    }

    /**
     * @param array $found
     *
     * @return array
     */
    protected function getBrands(array $found, array $categoryIds = []): array
    {
        if (empty($found)) {
            return [];
        }

        $brandId2count = $this->getBrandId2Count($found);

        if (count($brandId2count) > self::COUNT_TOP_BRANDS) {
            arsort($brandId2count);
            $q = $this->getDoctrine()->getManager()->createQuery('
                SELECT b.id, COALESCE(SUM(s.popularity), 0) AS popularity
                FROM AppBundle:Brand AS b
                LEFT OUTER JOIN AppBundle:BrandByCategoryStats AS s WITH s.brandId = b.id'.($categoryIds ? ' AND s.categoryId IN (:categoryIds)' : '').'
                WHERE b.id IN (:ids)
                GROUP BY b.id
                ORDER BY popularity DESC
            ');
            $q->setParameter('ids', array_keys($brandId2count));
            if ($categoryIds) {
                $q->setParameter('categoryIds', $categoryIds);
            }
            $top = array_fill_keys(array_slice(array_keys($q->getResult('ListHydrator')), 0, self::COUNT_TOP_BRANDS), 0);
        } else {
            $top = $brandId2count;
        }

        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT
                NEW AppBundle\Bus\Catalog\Finder\DTO\Brand (
                    b.id,
                    b.name,
                    b.sefName
                )
            FROM AppBundle:Brand AS b
            WHERE b.id IN (:ids)
            ORDER BY b.name
        ");
        $q->setParameter('ids', array_keys($brandId2count));
        $brands = $q->getResult('IndexByHydrator');

        if (isset($brandId2count[0])) {
            $brands[0] = new DTO\Brand(0, 'Прочее');
        }
        foreach ($brands as $id => $brand) {
            $brand->countProducts = $brandId2count[$id];
            $brand->isTop = isset($top[$id]);
        }

        return $brands;
    }

    /**
     * @param array $found
     *
     * @return array
     */
    protected function getAvailability(array $found): array
    {
        $availability = [];
        foreach ($found as $row) {
            $availability[$row['availability']] = $row['count(*)'];
        }
        foreach (Availability::getChoices($this->getUserIsEmployee()) as $type => $_) {
            if (!isset($availability[$type])) {
                $availability[$type] = 0;
            }
        }

        ksort($availability);
        $acc = 0;
        foreach ($availability as $index => $count) {
            $availability[$index] = $acc += $count;
        }

        return $availability;
    }

    /**
     * @param array $found
     *
     * @return array
     */
    protected function getNofilled(array $found): array
    {
        $mnemos = array_flip(Nofilled::getMnemos());
        $nofilled = [];
        foreach ($found as $result) {
            foreach ($result as $row) {
                $keys = array_keys($row);
                $values = array_values($row);
                if (1 == $values[0]) {
                    $nofilled[$mnemos[$keys[0]]] = $values[1];
                }
            }
        }

        return $nofilled;
    }
}
