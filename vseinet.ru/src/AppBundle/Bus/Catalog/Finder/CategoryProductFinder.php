<?php

namespace AppBundle\Bus\Catalog\Finder;

use AppBundle\Bus\Catalog\Query\DTO\Category;
use AppBundle\Bus\Brand\Query\DTO\Brand;
use AppBundle\Enum\DetailType;

class CategoryProductFinder extends AbstractProductFinder
{
    /**
     * @var Category
     */
    protected $category;

    /**
     * @var Brand
     */
    protected $brand;

    /**
     * @param iterable $values
     * @param Category $category
     * @param Brand    $brand
     *
     * @return self
     */
    public function setFilterData(iterable $values, Category $category, ?Brand $brand): self
    {
        $this->category = $category;
        $this->brand = $brand;
        $this->getFilter()->parse($values);

        return $this;
    }

    /**
     * @return DTO\Features
     */
    public function getFeatures(): DTO\Features
    {
        $qb = $this->getQueryBuilder();

        $qb->facet('FACET brand_id');

        if ($this->category->isTplEnabled) {
            $details = $this->getDetails();
            if (!empty($details)) {
                $qb->facet('FACET category_section_id');
                foreach ($details as $id => $detail) {
                    if (DetailType::CODE_NUMBER === $detail->typeCode) {
                        $qb->select($qb->getSelectDetailNumber($id));
                    } else {
                        $qb->facet("FACET details.{$id}");
                    }
                }
            }
        }

        $qb->criteria('category_id = '.$this->category->id);
        $name = $this->getFilter()->name;
        if (!empty($name)) {
            $qb->match($name);
        }

        $results = $qb->getFeatures();

        $features = new DTO\Features();
        $features->total = min($results[0][0]['total'], $qb::MAX_MATCHES);
        if (0 == $features->total) {
            return $features;
        }
        $features->price = new DTO\Range($results[1][0]['min_price'], $results[1][0]['max_price']);
        $features->availability = $this->getAvailability($results[3]);
        if ($this->getUserIsEmployee()) {
            $features->nofilled = $this->getNofilled(array_splice($results, 5, 5));
            $results = array_slice($results, 1);
        }

        $results = array_slice($results, 4);

        if ($this->category->isTplEnabled) {
            foreach (array_shift($results) as $row) {
                $ranges = [];
                foreach ($row as $key => $value) {
                    if ('total' == $key) {
                        continue;
                    }
                    list($bound, $id) = explode('_', $key, 2);
                    $ranges[$id][$bound.'_value'] = $value;
                }
                foreach ($ranges as $id => $range) {
                    $details[$id]->values = new DTO\Range($range['min_value'], $range['max_value']);
                }
            }
        } else {
            $results = array_slice($results, 1);
        }

        $features->brands = $this->getBrands(array_shift($results));

        if ($this->category->isTplEnabled) {
            $features->categorySections = $this->getCategorySections(array_shift($results));

            foreach ($results as $index => $result) {
                foreach ($result as $row) {
                    $keys = array_keys($row);
                    $values = array_values($row);
                    if (null === $values[0]) {
                        continue;
                    }
                    $id = str_replace('details.', '', $keys[0]);
                    $details[$id]->values[$values[0]] = $values[1];
                }
            }
            $detailValueIds = [];
            foreach ($details as $id => $detail) {
                if (empty($detail->values) || 2 > count($detail->values)) {
                    continue;
                }
                if (DetailType::CODE_ENUM === $detail->typeCode) {
                    $detailValueIds = array_merge($detailValueIds, array_keys($detail->values));
                }
                $features->details[$id] = $detail;
            }
            if (!empty($detailValueIds)) {
                $features->detailValues = $this->getDetailValues($detailValueIds);
            }
        }

        return $features;
    }

    /**
     * @return DTO\Facets
     */
    public function getFacets(): DTO\Facets
    {
        $qb = $this->getQueryBuilder();

        $qb->facet('FACET brand_id', $qb->getCriteriaBrands());

        if ($this->category->isTplEnabled) {
            $details = $this->getDetails();
            if (!empty($details)) {
                $qb->facet('FACET category_section_id', $qb->getCriteriaCategorySections());
                foreach ($details as $id => $detail) {
                    if (DetailType::CODE_NUMBER === $detail->typeCode) {
                        $qb->select($qb->getSelectDetailNumber($id), $qb->getCriteriaDetailNumber($id));
                    } elseif (DetailType::CODE_ENUM === $detail->typeCode) {
                        $qb->facet('FACET details.'.$id, $qb->getCriteriaDetailEnum($id));
                    } elseif (DetailType::CODE_BOOLEAN === $detail->typeCode) {
                        $qb->facet('FACET details.'.$id, $qb->getCriteriaDetailBoolean($id));
                    }
                }
            }
        }

        $qb->criteria('category_id = '.$this->category->id);
        $name = $this->getFilter()->name;
        if (!empty($name)) {
            $qb->match($name);
        }

        $results = $qb->getFacets();

        $facets = new DTO\Facets();
        $facets->total = min($results[0][0]['total'], $qb::MAX_MATCHES);
        if (0 == $facets->total) {
            return $facets;
        }
        $facets->price = new DTO\Range($results[1][0]['min_price'], $results[1][0]['max_price']);
        $facets->availability = $this->getAvailability($results[3]);
        if ($this->getUserIsEmployee()) {
            $facets->nofilled = $this->getNofilled(array_splice($results, 5, 5));
            $results = array_slice($results, 1);
        }

        $results = array_slice($results, 5);

        if ($this->category->isTplEnabled) {
            foreach ($results as $index => $result) {
                if (array_key_exists('total', $result[0])) {
                    $results = array_slice($results, $index + 1);
                    break;
                }
                foreach ($result as $row) {
                    $keys = array_keys($row);
                    $values = array_values($row);
                    $id = str_replace('min_', '', $keys[0]);
                    $facets->details[$id] = new DTO\Range($values[0], $values[1]);
                }
            }
        }

        $facets->brandIds = array_fill_keys(array_keys($this->getBrands(array_shift($results))), 1);

        if ($this->category->isTplEnabled) {
            $facets->categorySectionIds = array_fill_keys(array_keys($this->getCategorySections($results[1])), 1);
            $results = array_slice($results, 3);
            foreach ($results as $index => $result) {
                if (array_key_exists('total', $result[0])) {
                    continue;
                }
                foreach ($result as $row) {
                    $keys = array_keys($row);
                    $values = array_values($row);
                    if (null !== $values[0]) {
                        $id = str_replace('details.', '', $keys[0]);
                        $facets->details[$id][$values[0]] = 1;
                    }
                }
            }
        }

        return $facets;
    }

    /**
     * @return array
     */
    public function getProducts(): array
    {
        $qb = $this->getQueryBuilder();

        $qb->criteria($qb->getCriteriaBrands());

        if ($this->category->isTplEnabled) {
            $details = $this->getDetails();
            if (!empty($details)) {
                $qb->criteria($qb->getCriteriaCategorySections());
                foreach ($details as $id => $detail) {
                    if (DetailType::CODE_NUMBER === $detail->typeCode) {
                        $qb->criteria($qb->getCriteriaDetailNumber($id));
                    } elseif (DetailType::CODE_ENUM === $detail->typeCode) {
                        $qb->criteria($qb->getCriteriaDetailEnum($id));
                    } elseif (DetailType::CODE_BOOLEAN === $detail->typeCode) {
                        $qb->criteria($qb->getCriteriaDetailBoolean($id));
                    }
                }
            }
        }

        $qb->criteria('category_id = '.$this->category->id);
        $name = $this->getFilter()->name;
        if (!empty($name)) {
            $qb->match($name);
        }

        $products = $qb->getProducts();

        return $products;
    }

    protected function getCategorySections(array $found): array
    {
        $categorySectionId2count = [];
        foreach ($found as $row) {
            $categorySectionId2count[$row['category_section_id']] = $row['count(*)'];
        }
        if (1 < count($categorySectionId2count)) {
            $q = $this->getDoctrine()->getManager()->createQuery("
                SELECT
                    NEW AppBundle\Bus\Catalog\Finder\DTO\CategorySection (
                        cs.id,
                        cs.name
                    )
                FROM AppBundle:CategorySection AS cs
                WHERE cs.id IN (:ids)
            ");
            $q->setParameter('ids', $ids);
            $categorySections = $q->getResult('IndexByHydrator');
        }
        $categorySections[0] = new DTO\CategorySection(0, '');
        foreach ($categorySectionId2count as $id => $count) {
            if (isset($categorySections[$id])) {
                $categorySections[$id]->countProducts = $count;
            }
        }
        $categorySections[0]->countProducts = array_sum($categorySectionId2count);

        return $categorySections;
    }

    protected function getDetails(): array
    {
        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT
                NEW AppBundle\Bus\Catalog\Finder\DTO\Detail (
                    d.id,
                    d.name,
                    COALESCE(d.sectionId, 0),
                    d.typeCode,
                    mu.name
                )
            FROM AppBundle:Detail d
            INNER JOIN AppBundle:DetailGroup dg WITH dg.id = d.groupId
            LEFT OUTER JOIN AppBundle:MeasureUnit mu WITH mu.id = d.unitId
            WHERE dg.categoryId = :categoryId AND d.typeCode IN (:typeCodes) AND d.pid IS NULL
            ORDER BY dg.sortOrder, d.sortOrder
        ");
        $q->setParameter('categoryId', $this->category->id);
        $q->setParameter('typeCodes', DetailType::getFilterTypeCodes());

        return $q->getResult('IndexByHydrator');
    }

    protected function getDetailValues(array $ids): array
    {
        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT
                NEW AppBundle\Bus\Catalog\Finder\DTO\DetailValue (
                    dv.id,
                    dv.value
                )
            FROM AppBundle:DetailValue AS dv
            WHERE dv.id IN (:ids)
            ORDER BY dv.value
        ");
        $q->setParameter('ids', $ids);

        return $q->getResult('IndexByHydrator');
    }
}
