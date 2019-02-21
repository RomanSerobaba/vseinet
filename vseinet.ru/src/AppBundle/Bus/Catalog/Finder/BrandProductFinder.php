<?php

namespace AppBundle\Bus\Catalog\Finder;

class BrandProductFinder extends AbstractProductFinder
{
    public function getTemplate()
    {
        if ($this->template instanceof Template) {
            return $this->template;
        }

        $this->select('MIN(price) AS min_price, MAX(price) AS max_price');
        $this->facet('FACET category_id');
        $this->criteria('brand_id = '.$this->brand->id);

        $results = $this->queryTemplate();

        // @todo

    }
}
