<?php

namespace AppBundle\Bus\Catalog\Finder;

class SearchProductFinder extends AbstractProductFinder
{
    /**
     * @param iterable $values
     *
     * @return self
     */
    public function setFilterData(iterable $values): self
    {
        $filter = $this->getFilter()->parse($values);

        return $this;
    }

    /**
     * @return DTO\Features
     */
    public function getFeatures(): DTO\Features
    {
        $qb = $this->getQueryBuilder();

        $qb->facet('FACET category_id FACET brand_id');
        $qb->match($this->getFilter()->q);

        $results = $qb->getFeatures();

        $features = new DTO\Features();
        $features->total = $results[0][0]['total'];
        if (0 === $features->total) {
            return $features;
        }
        $features->price = new DTO\Range($results[1][0]['min_price'], $results[1][0]['max_price']);
        $features->availability = $this->getAvailability($results[3]);
        if ($this->getUserIsEmployee()) {
            $features->nofilled = $this->getNofilled(array_splice($results, 5, 5));
            $results = array_slice($results, 5);
        } else {
            $results = array_slice($results, 4);
        }

        $features->categories = $this->getCategories($results[1]);
        $features->brands = $this->getBrands($results[2]);

        return $features;
    }

    /**
     * @return DTO\Facets
     */
    public function getFacets(): DTO\Facets
    {
        $qb = $this->getQueryBuilder();

        $qb->facet('FACET category_id', $qb->getCriteriaCategories());
        $qb->facet('FACET brand_id', $qb->getCriteriaBrands());
        $qb->match($this->getFilter()->q);

        $results = $qb->getFacets();

        $facets = new DTO\Facets();
        $facets->total = $results[0][0]['total'];
        if (0 === $facets->total) {
            return $facets;
        }
        $facets->price = new DTO\Range($results[1][0]['min_price'], $results[1][0]['max_price']);
        $facets->availability = $this->getAvailability($results[3]);
        if ($this->getUserIsEmployee()) {
            $facets->nofilled = $this->getNofilled(array_splice($results, 5, 5));
            $results = array_slice($results, 5);
        } else {
            $results = array_slice($results, 4);
        }

        $facets->categoryIds = array_fill_keys(array_keys($this->getCategories($results[1])), 1);
        $facets->brandIds = array_fill_keys(array_keys($this->getBrands($results[3])), 1);

        return $facets;
    }

    /**
     * @return array
     */
    public function getProducts(): array
    {
        $qb = $this->getQueryBuilder();

        $qb->criteria($qb->getCriteriaCategories());
        $qb->criteria($qb->getCriteriaBrands());
        $qb->match($this->getFilter()->q);

        $products = $qb->getProducts();

        return $products;
    }
}
