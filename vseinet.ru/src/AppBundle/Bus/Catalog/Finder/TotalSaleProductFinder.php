<?php

namespace AppBundle\Bus\Catalog\Finder;

use AppBundle\Enum\ProductAvailabilityCode;

class TotalSaleProductFinder extends AbstractProductFinder
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

        $qb->facet('FACET category_id LIMIT 1000 FACET brand_id LIMIT 1000');
        $qb->criteria('is_total_sale = 1');

        $results = $qb->getFeatures();

        $features = new DTO\Features();
        $features->total = min($results[0][0]['total'], $qb::MAX_MATCHES);
        if (0 == $features->total) {
            return $features;
        }
        $features->price = new DTO\Range($results[1][0]['min_price'], $results[1][0]['max_price']);
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

        $qb->facet('FACET category_id LIMIT 1000', $qb->getCriteriaCategories());
        $qb->facet('FACET brand_id LIMIT 1000', $qb->getCriteriaBrands());
        $qb->criteria('is_total_sale = 1');
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
        if ($this->getUserIsEmployee()) {
            $facets->nofilled = $this->getNofilled(array_splice($results, 5, 5));
            $results = array_slice($results, 1);
        }

        $facets->categoryIds = $this->getCategoryId2Count($results[5]);
        $facets->brandIds = $this->getBrandId2Count($results[7]);

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
        $qb->criteria('is_total_sale = 1');
        $name = $this->getFilter()->name;
        if (!empty($name)) {
            $qb->match($name);
        }

        $products = $qb->getProducts();
        array_walk($products, function (&$product) {
            $product->availability = ProductAvailabilityCode::AVAILABLE;
        });

        return $products;
    }
}
