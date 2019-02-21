<?php

namespace AppBundle\Bus\Catalog\Finder;

class DetailProductFinder extends AbstractProductFinder
{
    public function getTemplate()
    {
        if ($this->template instanceof Template) {
            return $this->template;
        }

        $this->select('MIN(price) AS min_price, MAX(price) AS max_price');
        $this->facet('FACET category_id FACET brand_id');
        $this->criteria("details.{$this->detail->id} = {$this->detail->valueId}");

        $results = $this->queryTemplate();

        // @todo

    }
}
