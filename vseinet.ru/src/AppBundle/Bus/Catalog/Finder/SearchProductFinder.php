<?php

namespace AppBundle\Bus\Catalog\Finder;

class SearchProductFinder extends AbstractProductFinder
{
    public function getTemplate()
    {
        if ($this->template instanceof Template) {
            return $this->template;
        }

        $this->select('MIN(price) AS min_price, MAX(price) AS max_price');
        $this->facet('FACET category_id FACET brand_id');
        $this->match('q');

        $results = $this->queryTemplate();

        // @todo

    }
}
