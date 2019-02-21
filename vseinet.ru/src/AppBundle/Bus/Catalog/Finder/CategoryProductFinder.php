<?php

namespace AppBundle\Bus\Catalog\Finder;

use AppBundle\Bus\Catalog\Query\DTO\Category;
use AppBundle\Bus\Brand\Query\DTO\Brand;
use AppBundle\Bus\Catalog\Query\Filter\Filter;
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
     */
    public function setFilterData(iterable $values, Category $category, ?Brand $brand) : self
    {
        $this->category = $category;
        $this->brand = $brand;

        $this->filter = new Filter($values);
        unset($this->filter->categoryIds);
        if (null !== $this->brand) {
            unset($this->filter->brandIds);
        }

        return $this;
    }

    public function getFeatures() : Features
    {
        if ($this->features instanceof Features) {
            return $this->features;
        }

        $this->select('MIN(price) AS min_price, MAX(price) AS max_price');
        $this->facet('FACET brand_id');

        if ($this->category->isTplEnabled) {
            $details = $this->getDetails();
            if (!empty($details)) {
                $this->facet('FACET category_section_id');
                foreach ($details as $id => $detail) {
                    if (DetailType::CODE_NUMBER === $detail->typeCode) {
                        $this->select("MIN(DOUBLE(details.{$id})) AS min_{$id}, MAX(DOUBLE(details.{$id})) AS max_{$id}");
                    } else {
                        $this->facet("FACET details.{$id}");
                    }
                }
            }
        }

        $this->criteria('category_id = '.$this->category->id);
        if (null !== $this->brand) {
            $this->criteria('brand_id = '.$this->brand->id);
        }

        $results = $this->queryFilter();
        print_r($results); exit;

        $filter = new Filter();
        foreach (array_shift($results) as $row) {
            $this->filter->price = new Filter\Range($row['min_price'], $row['max_price']);
            if ($this->category->isTplEnabled) {
                unset($row['min_price'], $row['max_price']);
                $ranges = [];
                foreach ($row as $key => $value) {
                    list($bound, $id) = explode('_', $key, 2);
                    $ranges[$id][$bound.'_value'] = $value;
                }
                foreach ($ranges as $id => $range) {
                    if (isset($details[$id])) {
                        $details[$id]->values = new Filter\Range($range['min_value'], $range['max_value']);
                        $filter->details[$id] = $details[$id];
                    }
                }
            }
        }



    }


    public function getFacets()
    {

    }

    protected function getDetails()
    {
        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT
                NEW AppBundle\Bus\Catalog\Query\DTO\Filter\Detail (
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
}
