<?php

namespace AppBundle\Bus\Catalog\Finder;

use AppBundle\Container\ContainerAware;
use AppBundle\Bus\Catalog\Enum\Availability;

class AbstractProductFinder extends ContainerAware
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var Features
     */
    protected $features;

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


    protected function queryFilter()
    {
        $select = implode(', ', $this->select);
        $facets = implode(' ', $this->facets);
        $criteria = implode(' AND ', $this->criteria);

        $this->reset();

        $query = "
            SELECT {$select}
            FROM product_index_{$this->getGeoCity()->getRealId()}
            WHERE {$this->getCriteriaIsAlive()} AND {$criteria} AND {$this->getCriteriaAvailability()} AND {$this->getCriteriaNofilled()}
            {$facets}
            ;
            SELECT COUNT(*) AS total
            FROM product_index_{$this->getGeoCity()->getRealId()}
            WHERE {$this->getCriteriaIsAlive()} AND {$criteria} AND {$this->getCriteriaNofilled()}
            FACET availability
            ;
        ";
        if ($this->getUserIsEmployee()) {
            $query .= "
                SELECT COUNT(*) AS total
                FROM product_index_{$this->getGeoCity()->getRealId()}
                WHERE {$this->getCriteriaIsAlive()} AND {$criteria} AND {$this->getCriteriaAvailability()}
                FACET no_details
                FACET no_image
                FACET no_description
                FACET no_manufacturer_link
                FACET no_manual_link
                ;
            ";
        }

        return $this->get('sphinxql')->execute($query);
    }

    protected function queryFacets()
    {

    }

    protected function select($expression)
    {
        if (!empty($expression)) {
            $this->select[] = $expression;
        }

        return $this;
    }

    protected function facet($expression)
    {
        if (!empty($expression)) {
            $this->facets[] = $expression;
        }

        return $this;
    }

    protected function criteria($expression)
    {
        if (!empty($expression)) {
            $this->criteria[] = $expression;
        }

        return $this;
    }

    protected function reset()
    {
        $this->select = [];
        $this->facets = [];
        $this->criteria = [];
    }

    protected function getCriteriaIsAlive()
    {
        return 'is_forbidden = 0';
    }

    protected function getCriteriaAvailability()
    {
        $availability = $this->filter->availability;
        if (!$this->getUserIsEmployee()) {
            $availability = min($availability, Availability::ACTIVE);
        }

        return 'availability <= '.$availability;
    }

    protected function getCriteriaNofilled()
    {
        if (!$this->getUserIsEmployee()) {
            return '1 = 1';
        }

        return implode(' AND ', array_map(function($nofilled) {
            return $nofilled.' = 1';
        }, $this->getFilter()->data->nofilled));
    }
}
