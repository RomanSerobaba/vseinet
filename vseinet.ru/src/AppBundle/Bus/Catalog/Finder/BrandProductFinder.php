<?php

namespace AppBundle\Bus\Catalog\Finder;

use AppBundle\Bus\Brand\Query\DTO\Brand;

class BrandProductFinder extends AbstractProductFinder
{
    /**
     * @var Brand
     */
    protected $brand;

    /**
     * @param iterable $values
     *
     * @return self
     */
    public function setFilterData(iterable $values, Brand $brand): self
    {
        $this->brand = $brand;
        $filter = $this->getFilter()->parse($values);

        return $this;
    }

    /**
     * @return DTO\Features
     */
    public function getFeatures(): DTO\Features
    {
        $qb = $this->getQueryBuilder();

        $qb->select('MIN(price) AS min_price, MAX(price) AS max_price');
        $qb->facet('FACET category_id');
        $qb->criteria('brand_id = '.$this->brand->id);

        $results = $qb->getFeatures();

        $features = new DTO\Features();

        $features->price = new DTO\Range($results[0][0]['min_price'], $results[0][0]['max_price']);
        $features->categories = $this->getCategories($results[1]);
        $features->total = $results[2][0]['total'];
        $features->availability = $this->getAvailability($results[3]);
        if ($this->getUserIsEmployee()) {
            $features->nofilled = $this->getNofilled(array_slice($results, 5));
        }

        return $features;
    }
}
