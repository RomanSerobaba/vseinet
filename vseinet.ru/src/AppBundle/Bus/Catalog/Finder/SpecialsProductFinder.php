<?php

namespace AppBundle\Bus\Catalog\Finder;

class SpecialsProductFinder extends AbstractProductFinder
{
    public function setFilterData(iterable $values) : self
    {
        $filter = $this->getFilter()->setData($values);

        return $this;
    }

    public function getTemplate()
    {
        if ($this->template instanceof Template) {
            return $this->template;
        }

        $this->select('MIN(price) AS min_price, MAX(price) AS max_price');
        $this->facet('FACET category_id FACET brand_id');
        $this->criteria('availability = '.Availability::AVAILABLE);

        $results = $this->queryTemplate();

        // @todo

    }
}