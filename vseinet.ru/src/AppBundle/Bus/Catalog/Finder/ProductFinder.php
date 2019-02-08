<?php

namespace AppBundle\Bus\Catalog\Finder;

use AppBundle\Container\ContainerAware;
use AppBundle\Bus\Catalog\Enum\Availability;
use AppBundle\Bus\Catalog\Enum\Nofilled;
use AppBundle\Bus\Catalog\Enum\Sort;
use AppBundle\Bus\Catalog\Enum\SortDirection;
use AppBundle\Bus\Catalog\Query\DTO\Filter;

abstract class ProductFinder extends ContainerAware
{
    const MAX_MATCHES = 10000;
    const PER_PAGE = 25;

    /**
     * @var Filter\Data
     */
    protected $data;


    public function setData(Filter\Data $data): self
    {
        $this->data = $data;

        return $this;
    }

    abstract public function getFilter(): Filter;

    abstract public function getFacets(): Filter\Facets;

    public function getProductIds(): array
    {
        $query = "
            SELECT
                id,
                WEIGHT() AS weight
            FROM product_index_{$this->getGeoCity()->getRealId()}
            WHERE {$this->getCriteria()}
            ORDER BY {$this->getSortOrder()}
            LIMIT {$this->getOffsetPage()}
            OPTION {$this->getSearchOptions()};
        ";
        $results = $this->get('sphinxql')->execute($query);

        return array_map(function($row) { return intval($row['id']); }, $results[0]);
    }

    abstract protected function getCriteria(string $exclude = null): string;

    protected function getSortOrder(): string
    {
        $direction = SortDirection::ASC === $this->data->sortDirection ? 'ASC' : 'DESC';

        switch ($this->data->sort) {
            case Sort::PRICE:
                return "price_order ASC, price {$direction}";

            case Sort::NOVELTY:
                return "created_at {$direction}";

            case Sort::NAME:
                return "name {$direction}";
        }

        return "availability ASC, weight DESC, profit DESC";
    }

    protected function getOffsetPage(): string
    {
        $page = min($this->data->page, ceil(self::MAX_MATCHES / self::PER_PAGE));
        $offset = ($page - 1) * self::PER_PAGE;

        return $offset.', '.self::PER_PAGE;
    }

    protected function getSearchOptions(): string
    {
        return "ranker=expr('sum((4*lcs+2*(min_hit_pos==1)+exact_hit)*user_weight)*1000+bm25'),
                max_matches=".self::MAX_MATCHES;
    }

    protected function getSelectPrice(): string
    {
        return "MIN(INTEGER(price)) AS min_price, MAX(INTEGER(price)) AS max_price";
    }

    protected function getFacetAvailability(): string
    {
        return "FACET availability";
    }

    protected function getFacetsNofilled(): string
    {
        return "";
        return "
            FACET nofilled.".Nofilled::DETAILS."
            FACET nofilled.".Nofilled::IMAGES."
            FACET nofilled.".Nofilled::DESCRIPTION."
            FACET nofilled.".Nofilled::MANUFACTURER_LINK."
            FACET nofilled.".Nofilled::MANUAL_LINK."
        ";
    }

    protected function getCriteriaAlive(): string
    {
        return "killbill = 0 AND is_forbidden = 0";
    }

    protected function getCriteriaAvailability(): string
    {
        $availability = $this->data->availability;
        if (!$this->getUserIsEmployee()) {
            $availability = min($availability, Availability::ACTIVE);
        }

        return "availability <= {$availability}";
    }

    protected function getCriteriaNofilled(): ?string
    {
        if (!$this->getUserIsEmployee() || null === $this->data->nofilled) {
            return "";
        }

        return "";

        $criteria = [];
        foreach ($this->data->nofilled as $nofilled) {
            $criteria[] = "nofilled.{$nofilled} = 1";
        }

        return ' AND '.implode(' AND ', $criteria);
    }

    protected function getCriteriaPrice(): ?string
    {
        if (empty($this->data->price)) {
            return null;
        }

        if (null === $this->data->price->min) {
            return "price <= {$this->data->price->max}";
        }

        if (null === $this->data->price->max) {
            return "price >= {$this->data->price->min}";
        }

        return "price BETWEEN {$this->data->price->min} AND {$this->data->price->max}";
    }

    protected function getCriteriaCategories(Filter\Category ...$categories): ?string
    {
        $categoryIds = $this->data->categoryIds;
        if (empty($categoryIds)) {
            return null;
        }

        if (!empty($categoryIds[-1])) {
            if (!empty($categories[-1]->includeIds)) {
                $categoryIds = array_merge($categoryIds, $categories[-1]->includeIds);
            }
            unset($categoryIds[-1]);
        }

        return "category_id IN (".implode(',', $categoryIds).")";
    }

    protected function getCriteriaBrands(Filter\Brand ...$brands): ?string
    {
        $brandIds = $this->data->brandIds;
        if (empty($brandIds)) {
            return null;
        }

        if (!empty($brandIds[-1])) {
            if (!empty($brands[-1]->includeIds)) {
                $brandIds = array_merge($brandIds, $brands[-1]->includeIds);
            }
            unset($brandIds[-1]);
        }

        return "brand_id IN (".implode(',', $brandIds).")";
    }
}
