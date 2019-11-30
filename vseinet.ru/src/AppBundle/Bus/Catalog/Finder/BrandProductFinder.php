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
     * @param Brand    $brand
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

        $qb->facet('FACET category_id LIMIT 1000');
        $qb->criteria('brand_id = '.$this->brand->id);
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
            $features->nofilled = $this->getNofilled(array_splice($results, 5, 6));
            $results = array_slice($results, 1);
        }

        $features->categories = $this->getCategories($results[5]);

        return $features;
    }

    /**
     * @return DTO\Facets
     */
    public function getFacets(): DTO\Facets
    {
        $qb = $this->getQueryBuilder();

        $qb->facet('FACET category_id LIMIT 1000', $qb->getCriteriaCategories());
        $qb->criteria('brand_id = '.$this->brand->id);
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
            $facets->nofilled = $this->getNofilled(array_splice($results, 5, 6));
            $results = array_slice($results, 1);
        }

        $facets->categoryIds = $this->getCategoryId2Count($results[5]);

        return $facets;
    }

    /**
     * @return array
     */
    public function getProducts(): array
    {
        $qb = $this->getQueryBuilder();

        $qb->criteria($qb->getCriteriaCategories());
        $qb->criteria('brand_id = '.$this->brand->id);
        $name = $this->getFilter()->name;
        if (!empty($name)) {
            $qb->match($name);
        }

        $products = $qb->getProducts();

        return $products;
    }
}
