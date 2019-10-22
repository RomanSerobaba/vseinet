<?php

namespace AppBundle\Bus\Catalog\Finder;

use AppBundle\Container\ContainerAware;
use AppBundle\Bus\Catalog\Enum\Availability;
use AppBundle\Bus\Catalog\Enum\Nofilled;

class AbstractProductFinder extends ContainerAware
{
    public const COUNT_GET_BRANDS = 50;
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
    protected function getCategories(array $found): array
    {
        if (empty($found)) {
            return [];
        }

        $categoryId2count = [];
        foreach ($found as $row) {
            $categoryId2count[$row['category_id']] = $row['count(*)'];
        }

        $q = $this->getDoctrine()->getManager()->createQuery('
            SELECT
                c.id,
                c.name,
                c.isTplEnabled,
                c2.id AS id2,
                c2.name AS name2,
                CASE WHEN c.isTplEnabled = true THEN 1 ELSE 2 END AS HIDDEN ORD1,
                CASE WHEN c.isTplEnabled = true THEN c.name ELSE c2.name END AS HIDDEN ORD2
            FROM AppBundle:Category c
            INNER JOIN AppBundle:CategoryPath cp WITH cp.id = c.id AND cp.plevel = 2
            INNER JOIN AppBundle:Category c2 WITH c2.id = cp.pid
            WHERE c.id IN (:ids)
            ORDER BY ORD1, ORD2, c.name
        ');
        $q->setParameter('ids', array_keys($categoryId2count));
        $categories = $q->getResult();

        $main = [];
        $tree = [];
        foreach ($categories as $category) {
            $id = $category['id'];
            if ($category['isTplEnabled']) {
                $main[$id] = new DTO\Category($id, $category['name'], $categoryId2count[$id]);
            } else {
                $id2 = $category['id2'];
                if (empty($tree[$id2])) {
                    $tree[$id2] = new DTO\Category($id2, $category['name2']);
                }
                $tree[$id2]->children[$id] = new DTO\Category($id, $category['name'], $categoryId2count[$id]);
                $tree[$id2]->countProducts += $categoryId2count[$id];
            }
            if (!empty($main)) {
                $tree[0] = new DTO\Category(0, 'Каталог', 0, $main);
            }
            // @todo
            // if (!empty($tree)) {
            //     if (20 < count($tree)) {
            //         $other = new Category(-1, 'Прочие');
            //         foreach ($tree as $id2 => $cat2) {
            //             if (1 == count($cat2->children)) {
            //                 $cat2->name .= ' » '.reset($cat2->children)->name;
            //                 $other->children[$cat2->id] = $cat2;
            //             }
            //         }
            //         if (!empty($other->children)) {
            //             $tree[-1] = $other;
            //         }
            //     }
            // }
        }

        return $tree;
    }

    /**
     * @param array $found
     *
     * @return array
     */
    protected function getBrands(array $found): array
    {
        if (empty($found)) {
            return [];
        }

        $brandId2count = [];
        foreach ($found as $row) {
            $brandId2count[$row['brand_id']] = $row['count(*)'];
        }
        arsort($brandId2count);

        // if (self::COUNT_GET_BRANDS < count($brandId2count)) {
        //     $otherBrandId2Count = array_slice($brandId2count, self::COUNT_GET_BRANDS, null, true);
        //     $brandId2count = array_slice($brandId2count, 0, self::COUNT_GET_BRANDS, true);
        // }

        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT
                NEW AppBundle\Bus\Catalog\Finder\DTO\Brand (
                    b.id,
                    b.name
                ),
                CASE WHEN b.id > 0 THEN 1 ELSE 2 END AS HIDDEN ORD
            FROM AppBundle:Brand AS b
            WHERE b.id IN (:ids)
            ORDER BY ORD, b.name
        ");
        $q->setParameter('ids', array_keys($brandId2count));
        $brands = $q->getResult('IndexByHydrator');

        $brandId2count = array_intersect_key($brandId2count, $brands);

        foreach ($brandId2count as $id => $count) {
            $brands[$id]->countProducts = $count;
        }
        // $brandId2count = array_slice($brandId2count, 0, self::COUNT_TOP_BRANDS, true);
        foreach ($brandId2count as $id => $count) {
            $brands[$id]->isTop = true;
        }

        // if (!empty($otherBrandId2Count)) {
        //     $brands[-1] = new DTO\Brand(-1, 'Прочие');
        //     $brands[-1]->countProducts = array_sum($otherBrandId2Count);
        //     $brands[-1]->includeIds = array_keys($otherBrandId2Count);
        // }

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
