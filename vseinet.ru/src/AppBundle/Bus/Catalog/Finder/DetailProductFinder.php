<?php

namespace AppBundle\Bus\Catalog\Finder;

use AppBundle\Bus\Catalog\Query\DTO\Detail;

class DetailProductFinder extends AbstractProductFinder
{
    /**
     * @var Detail
     */
    protected $detail;

    /**
     * @param iterable $values
     * @param Detail   $detail
     *
     * @return self
     */
    public function setFilterData(iterable $values, Detail $detail): self
    {
        $this->detail = $detail;
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
        $qb->select("LENGTH(details.{$this->detail->id}) AS has_detail");
        $qb->criteria('has_detail = 1');
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

        $features->categories = $this->getCategories($results[5]);
        $features->brands = $this->getBrands($results[6]);

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
        $qb->select("LENGTH(details.{$this->detail->id}) AS has_detail");
        $qb->criteria('has_detail = 1');
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

        $facets->categoryIds = array_fill_keys(array_keys($this->getCategories($results[5])), 1);
        $facets->brandIds = array_fill_keys(array_keys($this->getBrands($results[7])), 1);

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
        $qb->select("LENGTH(details.{$this->detail->id}) AS has_detail");
        $qb->criteria('has_detail = 1');
        $name = $this->getFilter()->name;
        if (!empty($name)) {
            $qb->match($name);
        }

        $products = $qb->getProducts();

        return $products;
    }
}
