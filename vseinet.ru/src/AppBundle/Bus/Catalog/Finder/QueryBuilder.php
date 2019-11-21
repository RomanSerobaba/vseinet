<?php

namespace AppBundle\Bus\Catalog\Finder;

use AppBundle\Container\ContainerAware;
use AppBundle\Bus\Catalog\Enum\Availability;
use AppBundle\Bus\Catalog\Enum\Nofilled;
use AppBundle\Bus\Catalog\Enum\Sort;
use AppBundle\Bus\Catalog\Enum\SortDirection;
use AppBundle\Bus\Catalog\Query\GetProductsQuery;
use AppBundle\Bus\Cart\Query\GetInfoQuery as GetCartInfoQuery;

class QueryBuilder extends ContainerAware
{
    public const MAX_MATCHES = 10000;
    public const PER_PAGE = 25;

    /**
     * @var array
     */
    protected $select = [];

    /**
     * @var array
     */
    protected $facets = [];

    /**
     * @var array
     */
    protected $criteria = [];

    /**
     * @var array
     */
    protected $match = [];

    /**
     * @return array
     */
    public function getFeatures(): array
    {
        $this->criteria[] = $this->getCriteriaIsAlive();
        if (!empty($this->match)) {
            $this->criteria[] = "MATCH('".$this->escape($this->escape(implode(' ', $this->match)))."')";
        }

        // total, all filters
        $criteria = $this->criteria;
        $criteria[] = $this->getCriteriaAvailability();
        $criteria = implode(' AND ', array_filter($criteria));
        $query[] = "
            SELECT COUNT(*) AS total
            FROM product_index_{$this->getGeoCity()->getRealId()}
            WHERE {$criteria}
            ;
            SELECT {$this->getSelectPrice()}
            FROM product_index_{$this->getGeoCity()->getRealId()}
            WHERE {$criteria}
            ;
        ";

        // availability
        $criteria = implode(' AND ', array_filter($this->criteria));
        $query[] = "
            SELECT COUNT(*) AS total
            FROM product_index_{$this->getGeoCity()->getRealId()}
            WHERE {$criteria}
            FACET availability
            ;
        ";

        // nofilled
        if ($this->getUserIsEmployee()) {
            $criteria = $this->criteria;
            $criteria[] = $this->getCriteriaAvailability();
            $criteria = implode(' AND ', array_filter($criteria));
            $query[] = "
                SELECT COUNT(*) AS total
                FROM product_index_{$this->getGeoCity()->getRealId()}
                WHERE {$criteria}
                {$this->getNofilledFacets()}
                ;
            ";
        }

        $select = implode(', ', array_merge($this->select, ['COUNT(*) AS total']));
        $facets = implode(' ', $this->facets);
        $criteria = $this->criteria;
        $criteria[] = $this->getCriteriaAvailability();
        $criteria = implode(' AND ', array_filter($criteria));
        $query[] = "
            SELECT {$select}
            FROM product_index_{$this->getGeoCity()->getRealId()}
            WHERE {$criteria}
            {$facets}
            ;
        ";

        return $this->get('sphinx')->createQuery()->setQuery(implode("\n", $query))->getResults();
    }

    /**
     * @return array
     */
    public function getFacets(): array
    {
        $this->criteria[] = $this->getCriteriaIsAlive();
        if (!empty($this->match)) {
            $this->criteria[] = "MATCH('".$this->escape($this->escape(implode(' ', $this->match)))."')";
        }

        // total, all filters
        $criteria = $this->criteria;
        $criteria[] = $this->getCriteriaPrice();
        $criteria[] = $this->getCriteriaAvailability();
        if ($this->getUserIsEmployee()) {
            $criteria[] = $this->getCriteriaNofilled();
        }
        $criteria = implode(' AND ', array_filter($criteria));
        $query[] = "
            SELECT COUNT(*) AS total
            FROM product_index_{$this->getGeoCity()->getRealId()}
            WHERE {$criteria}
            ;
        ";

        // price
        $criteria = $this->criteria;
        $criteria[] = $this->getCriteriaAvailability();
        if ($this->getUserIsEmployee()) {
            $criteria[] = $this->getCriteriaNofilled();
        }
        $criteria = implode(' AND ', array_filter($criteria));
        $query[] = "
            SELECT {$this->getSelectPrice()}
            FROM product_index_{$this->getGeoCity()->getRealId()}
            WHERE {$criteria}
            ;
        ";

        // availability
        $criteria = $this->criteria;
        $criteria[] = $this->getCriteriaPrice();
        if ($this->getUserIsEmployee()) {
            $criteria[] = $this->getCriteriaNofilled();
        }
        $criteria = implode(' AND ', array_filter($criteria));
        $query[] = "
            SELECT COUNT(*) AS total
            FROM product_index_{$this->getGeoCity()->getRealId()}
            WHERE {$criteria}
            FACET availability
            ;
        ";

        // nofilled
        if ($this->getUserIsEmployee()) {
            $criteria = $this->criteria;
            $criteria[] = $this->getCriteriaPrice();
            $criteria[] = $this->getCriteriaAvailability();
            $criteria = implode(' AND ', array_filter($criteria));
            $query[] = "
                SELECT COUNT(*) AS total
                FROM product_index_{$this->getGeoCity()->getRealId()}
                WHERE {$criteria}
                {$this->getNofilledFacets()}
                ;
            ";
        }

        // ranges
        foreach ($this->select as $select) {
            $criteria = array_filter($this->criteria, function ($criteria, $expression) use ($select) {
                return $select !== $expression && !empty($criteria);
            }, ARRAY_FILTER_USE_BOTH);
            $criteria[] = $this->getCriteriaPrice();
            $criteria[] = $this->getCriteriaAvailability();
            if ($this->getUserIsEmployee()) {
                $criteria[] = $this->getCriteriaNofilled();
            }
            $criteria = implode(' AND ', array_filter($criteria));
            $query[] = "
                SELECT {$select}
                FROM product_index_{$this->getGeoCity()->getRealId()}
                WHERE {$criteria}
                ;
            ";
        }

        // enums, booleans
        foreach ($this->facets as $facet) {
            $criteria = array_filter($this->criteria, function ($criteria, $expression) use ($facet) {
                return $facet !== $expression && !empty($criteria);
            }, ARRAY_FILTER_USE_BOTH);
            $criteria[] = $this->getCriteriaPrice();
            $criteria[] = $this->getCriteriaAvailability();
            if ($this->getUserIsEmployee()) {
                $criteria[] = $this->getCriteriaNofilled();
            }
            $criteria = implode(' AND ', array_filter($criteria));
            $query[] = "
                SELECT COUNT(*) AS total
                FROM product_index_{$this->getGeoCity()->getRealId()}
                WHERE {$criteria}
                {$facet}
                ;
            ";
        }

        return $this->get('sphinx')->createQuery()->setQuery(implode("\n", $query))->getResults();
    }

    /**
     * @return array
     */
    public function getProducts($isSearch = false): array
    {
        $this->criteria[] = $this->getCriteriaIsAlive();
        $this->criteria[] = $this->getCriteriaPrice();
        $this->criteria[] = $this->getCriteriaAvailability();
        if ($this->getUserIsEmployee()) {
            $this->criteria[] = $this->getCriteriaNofilled();
        }
        if (!empty($this->match)) {
            $this->criteria[] = "MATCH('".$this->rankingExactWords($this->escape($this->escape(implode(' ', $this->match))))."')";
        }
        $criteria = implode(' AND ', array_filter($this->criteria));

        $filter = $this->getFilter();
        $sortDirection = SortDirection::ASC === $filter->sortDirection ? 'ASC' : 'DESC';
        if (Sort::PRICE === $filter->sort) {
            $sort = 'price_order ASC, price '.$sortDirection;
        } elseif (Sort::NOVELTY === $filter->sort) {
            $sort = 'created_at '.(SortDirection::ASC === $filter->sortDirection ? 'DESC' : 'ASC');
        } elseif (Sort::NAME === $filter->sort) {
            $sort = 'name '.$sortDirection;
        } elseif (Sort::MARGING === $filter->sort) {
            $sort = 'availability ASC, profit DESC';
        } else {
            $sort = $isSearch ? 'weight DESC, availability ASC, rating DESC, price ASC' : 'availability ASC, weight DESC, rating DESC, price ASC';
        }

        $page = min($filter->page, ceil(self::MAX_MATCHES / self::PER_PAGE));
        $offset = ($page - 1) * self::PER_PAGE;

        // $options = 'ranker=expr(\'sum((word_count + IF(5-min_best_span_pos > 0, 1, 0)) * user_weight) * 100 + bm25 + availability * 10\'), max_matches='.self::MAX_MATCHES;
        $options = 'ranker=expr(\'sum(sum_idf * 10 + if(min_best_span_pos < 5, 5, 0)) + if(availability < 4, 4 - availability, 0) * 4 + if(category_average_price > 500000, 2, if(category_average_price > 80000, 1, 0)) * 10 + if(popularity > 50, 10, 0) + if(name_length < 150, 10, 0)\'), max_matches='.self::MAX_MATCHES;

        $query = "
            SELECT
                id,
                WEIGHT() AS weight
            FROM product_index_{$this->getGeoCity()->getRealId()}
            WHERE {$criteria}
            ORDER BY {$sort}
            LIMIT {$offset}, ".self::PER_PAGE."
            OPTION {$options}
            ;
        ";

        $results = $this->get('sphinx')->createQuery()->setQuery($query)->getResults();

        $ids = [];
        if (!empty($results[0])) {
            $ids = array_map(function ($row) { return intval($row['id']); }, $results[0]);
        }
        if (1 === count($this->match) and is_numeric($this->match[0])) {
            $ids = array_merge([intval($this->match[0])], $ids);
        }
        if (empty($ids)) {
            return [];
        }

        $products = $this->get('query_bus')->handle(new GetProductsQuery(['ids' => $ids]));
        $cartInfo = $this->get('query_bus')->handle(new GetCartInfoQuery());

        foreach ($products as $id => $product) {
            $product->quantityInCart = $cartInfo->products[$product->id]->quantity ?? 0;
        }

        return $products;
    }

    /**
     * @param string      $expression
     * @param string|null $criteria
     *
     * @return self
     */
    public function select(string $expression, ?string $criteria = null): self
    {
        if (!empty($expression)) {
            $this->select[] = $expression;
            if (!empty($criteria)) {
                $this->criteria[$expression] = $criteria;
            }
        }

        return $this;
    }

    /**
     * @param string      $expression
     * @param string|null $criteria
     *
     * @return self
     */
    public function facet($expression, ?string $criteria = null): self
    {
        if (!empty($expression)) {
            $this->facets[] = $expression;
            if (!empty($criteria)) {
                $this->criteria[$expression] = $criteria;
            }
        }

        return $this;
    }

    /**
     * @param string $expression
     *
     * @return self
     */
    public function criteria(string $expression): self
    {
        if (!empty($expression)) {
            $this->criteria[] = $expression;
        }

        return $this;
    }

    /**
     * @param string $words
     *
     * @return self
     */
    public function match(string $words): self
    {
        if (1 < mb_strlen($words)) {
            $this->match[] = $words;
        }

        return $this;
    }

    /**
     * @return self
     */
    public function reset(): self
    {
        $this->select = [];
        $this->facets = [];
        $this->criteria = [];
        $this->match = [];

        return $this;
    }

    /**
     * @return Filter
     */
    public function getFilter(): Filter
    {
        return $this->get('catalog.product.finder.filter');
    }

    /**
     * @return string
     */
    public function getSelectPrice()
    {
        return 'MIN(price) AS min_price, MAX(price) AS max_price';
    }

    /**
     * @return string
     */
    public function getCriteriaPrice(): string
    {
        $price = $this->getFilter()->price;
        if (null === $price) {
            return '';
        }

        if (null === $price->min) {
            return sprintf('price <= %s', str_replace(',', '.', $price->max));
        }
        if (null === $price->max) {
            return sprintf('price >= %s', str_replace(',', '.', $price->min));
        }

        return sprintf('price BETWEEN %d AND %d', $price->min, $price->max);
    }

    /**
     * @return string
     */
    public function getCriteriaCategories(): string
    {
        $categoryIds = $this->getFilter()->categoryIds;
        if (empty($categoryIds)) {
            return '';
        }

        if (!empty($categoryIds[-1])) {
            if (!empty($categoryIds[-1]->includeIds)) {
                $categoryIds = array_merge($categoryIds, $categoryIds[-1]->includeIds);
            }
            unset($categoryIds[-1]);
        }

        return 'category_id IN ('.implode(', ', $categoryIds).')';
    }

    /**
     * @return string
     */
    public function getCriteriaBrands(): string
    {
        $brandIds = $this->getFilter()->brandIds;
        if (empty($brandIds)) {
            return '';
        }

        if (!empty($brandIds[-1])) {
            if (!empty($brandIds[-1]->includeIds)) {
                $brandIds = array_merge($brandIds, $brandIds[-1]->includeIds);
            }
            unset($brandIds[-1]);
        }

        return 'brand_id IN ('.implode(', ', $brandIds).')';
    }

    /**
     * @return string
     */
    public function getCriteriaIsAlive(): string
    {
        return 'is_forbidden = 0 AND killbill = 0';
    }

    /**
     * @return string
     */
    public function getCriteriaAvailability(): string
    {
        $availability = $this->getFilter()->getAvailability();
        if (!$this->getUserIsEmployee()) {
            $availability = min($availability, Availability::ACTIVE);
        }

        return 'availability <= '.$availability;
    }

    /**
     * @return string
     */
    public function getCriteriaNofilled(): string
    {
        if (!$this->getUserIsEmployee()) {
            return '';
        }

        $mnemos = Nofilled::getMnemos();

        return implode(' AND ', array_map(function ($nofilled) use ($mnemos) {
            return $mnemos[$nofilled].' = 1';
        }, $this->getFilter()->nofilled));
    }

    /**
     * @return string
     */
    public function getNofilledFacets(): string
    {
        return implode("\n", array_map(function ($nofilled) {
            return 'FACET '.$nofilled;
        }, Nofilled::getMnemos()));
    }

    /**
     * @return string
     */
    public function getCriteriaCategorySections(): string
    {
        $categorySectionIds = $this->getFilter()->categorySectionIds;
        if (empty($categorySectionIds)) {
            return '';
        }

        return 'category_section_id IN ('.implode(', ', $categorySectionIds).')';
    }

    /**
     * @param int $id
     *
     * @return string
     */
    public function getSelectDetailNumber(int $id): string
    {
        return sprintf('MIN(DOUBLE(details.%1$d)) AS min_%1$d, MAX(DOUBLE(details.%1$d)) AS max_%1$d', $id);
    }

    /**
     * @param int $id
     *
     * @return string
     */
    public function getCriteriaDetailNumber(int $id): string
    {
        $filter = $this->getFilter();
        if (!array_key_exists($id, $filter->details)) {
            return '';
        }

        $detail = $filter->details[$id];

        if (null === $detail->min) {
            return sprintf('details.%d <= %s', $id, str_replace(',', '.', $detail->max));
        }
        if (null === $detail->max) {
            return sprintf('details.%d >= %s', $id, str_replace(',', '.', $detail->min));
        }

        return sprintf('details.%d BETWEEN %s AND %s', $id, str_replace(',', '.', $detail->min), str_replace(',', '.', $detail->max));
    }

    /**
     * @param int $id
     *
     * @return string
     */
    public function getCriteriaDetailEnum(int $id): string
    {
        $filter = $this->getFilter();
        if (!array_key_exists($id, $filter->details)) {
            return '';
        }

        return sprintf('details.%d IN (%s)', $id, implode(', ', $filter->details[$id]));
    }

    /**
     * @param int $id
     *
     * @return string
     */
    public function getCriteriaDetailBoolean(int $id): string
    {
        $filter = $this->getFilter();
        if (!array_key_exists($id, $filter->details)) {
            return '';
        }

        return sprintf('details.%d = %d', $id, $filter->details[$id]);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function escape(string $string): string
    {
        $from = ['\\',   '(',  ')',  '|',  '-',  '!',  '@',  '~',  '"',  "'",  '&',  '/',  '^',  '$',  '='];
        $to = ['\\\\', '\(', '\)', '\|', '\-', '\!', '\@', '\~', '\"', "\'", '\&', '\/', '\^', '\$', '\='];

        return str_replace($from, $to, $string);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function rankingExactWords(string $string): string
    {
        $pieces = explode(' ', $string);
        $result = [];

        foreach ($pieces as $piece) {
            if (strlen($piece)) {
                $result[] = '(='.$piece.'^10|'.$piece.'*^2|*'.$piece.'*)';
            }
        }

        return implode(' ', $result);
    }
}
