<?php 

namespace SiteBundle\Bus\Catalog\Finder;

use AppBundle\Container\ContainerAware;
use SiteBundle\Bus\Catalog\Enum\Availability;
use SiteBundle\Bus\Catalog\Enum\Nofilled;
use SiteBundle\Bus\Catalog\Enum\Sort;
use SiteBundle\Bus\Catalog\Enum\SortDirection;
use SiteBundle\Bus\Catalog\Query\DTO\Filter;

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

    public function getCityId(): int
    {
        return $this->get('city.identity')->getId();
    }

    abstract public function getFilter(): Filter;

    abstract public function getFacets(): Filter\Facets;

    public function getProductIds(): array 
    {
        $query = "
            SELECT 
                id, 
                WEIGHT() AS weight 
            FROM base_product 
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
                return "price_order.{$this->getCityId()} ASC, price.{$this->getCityId()} {$direction}";

            case Sort::NOVELTY:
                return "created_at {$direction}";

            case Sort::NAME:
                return "name {$direction}";
        }

        return "availability.{$this->getCityId()} ASC, weight DESC, profit DESC";
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
        return "MIN(INTEGER(price.{$this->getCityId()})) AS min_price, MAX(INTEGER(price.{$this->getCityId()})) AS max_price";
    } 

    protected function getFacetAvailability(): string
    {
        return "FACET availability.{$this->getCityId()}";
    }

    protected function getFacetsNofilled(): string
    {
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
        if ($this->get('user.identity')->isClient()) {
            $availability = min($availability, Availability::ACTIVE);
        }

        return "availability.{$this->getCityId()} <= {$availability}";
    }

    protected function getCriteriaPrice(): ?string
    {
        if (empty($this->data->price)) {
            return null;
        }

        if (null === $this->data->price->min) {
            return "price.{$this->getCityId()} <= {$this->data->price->max}";
        }

        if (null === $this->data->price->max) {
            return "price.{$this->getCityId()} >= {$this->data->price->min}";
        }

        return "price.{$this->getCityId()} BETWEEN {$this->data->price->min} AND {$this->data->price->max}";
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
