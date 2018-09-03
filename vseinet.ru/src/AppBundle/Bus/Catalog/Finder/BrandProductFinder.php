<?php 

namespace AppBundle\Bus\Catalog\Finder;

use AppBundle\Bus\Catalog\Enum\Availability;
use AppBundle\Bus\Catalog\Enum\Nofilled;
use AppBundle\Bus\Brand\Query\DTO\Brand;
use AppBundle\Bus\Catalog\Query\DTO\Filter;

class BrandProductFinder extends ProductFinder
{
    /**
     * Brand
     */
    protected $brand;

    /**
     * @var Filter
     */
    protected $filter;


    public function setBrand(Brand $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getFilter(): Filter
    {
        if ($this->filter instanceof Filter) {
            return $this->filter;
        }

        $query = "
            SELECT {$this->getSelectPrice()}
            FROM base_product
            WHERE {$this->getMainCriteria()} AND {$this->getCriteriaAlive()} AND {$this->getCriteriaAvailability()} {$this->getCriteriaNofilled()}
            FACET category_id LIMIT 1000
            ;
            SELECT COUNT(*) AS total 
            FROM base_product
            WHERE {$this->getMainCriteria('brands')} AND {$this->getCriteriaAlive()} AND {$this->getCriteriaAvailability()} 
            {$this->getFacetsNofilled()}
            ;
            SELECT COUNT(*) AS total 
            FROM base_product
            WHERE {$this->getMainCriteria()} AND {$this->getCriteriaAlive()} {$this->getCriteriaNofilled()}
            {$this->getFacetAvailability()}
            ;
            SELECT COUNT(*) AS total 
            FROM base_product 
            WHERE {$this->getMainCriteria()} AND {$this->getCriteriaAlive()} AND {$this->getCriteriaAvailability()} {$this->getCriteriaNofilled()}
            ;
        ";
        $results = $this->get('sphinxql')->execute($query);

        $this->filter = new Filter();
        
        $result = array_shift($results);
        $this->filter->price = new Filter\Range($result[0]['min_price'], $result[0]['max_price']);
        
        $categoryId2count = [];
        foreach (array_shift($results) as $row) {
            $categoryId2count[$row['category_id']] = $row['count(*)'];
        }
        $this->filter->categories = Block\Categories::build($categoryId2count, $this->getDoctrine()->getManager());

        array_shift($results);

        foreach (Nofilled::getOptions() as $type => $_) {
            $row = array_shift($results);
            $this->filter->nofilled[$type] = array_key_exists(1, $row) ? $row[1]['count(*)'] : 0;
        }

        array_shift($results);

        $geoCityId = $this->getGeoCity()->getRealId();
        foreach (array_shift($results) as $row) {
            $availability[$row['availability.'.$geoCityId]] = $row['count(*)'];
        }
        foreach (Availability::getOptions($this->getUserIsEmployee()) as $type => $_) {
            if (!isset($availability[$type])) {
                $availability[$type] = 0;
            }
        }
        $this->filter->availability = Block\Availability::build($availability);

        $this->filter->total = $results[0][0]['total'];

        return $this->filter;
    }

    public function getFacets(): Filter\Facets
    {
        $filter = $this->getFilter();

        $query = "
            SELECT COUNT(*) AS total
            FROM base_product 
            WHERE {$this->getCriteria()}
            ;
            SELECT {$this->getSelectPrice()}
            FROM base_product
            WHERE {$this->getCriteria('price')}
            ;
            SELECT COUNT(*) AS total
            FROM base_product
            WHERE {$this->getCriteria('categories')}
            FACET category_id LIMIT 1000
            ;
        ";
        $results = $this->get('sphinxql')->execute($query);

        $facets = new Filter\Facets();

        $result = array_shift($results);
        $facets->total = $result[0]['total'];

        $result = array_shift($results);
        $facets->price = new Filter\Range($result[0]['min_price'], $result[0]['max_price']);

        array_shift($results);
        foreach (array_shift($results) as $row) {
            if (isset($filter->categories[$row['category_id']])) {
                $facets->categoryIds[$row['category_id']] = 1;
            } else {
                $facets->categoryIds[-1] = 1;
            }
        }

        return $facets;
    }

    protected function getCriteria(string $exclude = null): string
    {
        $filter = $this->getFilter();

        $criteria = "{$this->getMainCriteria()} AND {$this->getCriteriaAlive()} AND {$this->getCriteriaAvailability()} {$this->getCriteriaNofilled()}";
        if ('price' != $exclude && ($condition = $this->getCriteriaPrice())) {
            $criteria .= " AND $condition";
        }
        if ('categories' != $exclude && ($condition = $this->getCriteriaCategories(...$filter->categories))) {
            $criteria .= " AND $condition";
        }

        return $criteria;
    }

    protected function getMainCriteria(): string 
    {
        return "brand_id = {$this->brand->id}";
    }
}
