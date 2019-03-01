<?php

namespace AppBundle\Bus\Catalog\Finder;

use AppBundle\Bus\Catalog\Query\DTO\Detail;

class DetailProductFinder extends AbstractProductFinder
{
    /**
     * @var Detail
     */
    protected $detail;

    /**
     * @param iterable $values
     *
     * @return self
     */
    public function setFilterData(iterable $values, Brand $detail): self
    {
        $this->detail = $detail;
        $filter = $this->getFilter()->parse($values);

        return $this;
    }

    /**
     * @return DTO\Features
     */
    public function getFeatures(): DTO\Features
    {
        $qb = $this->getQueryBuilder();

        $qb->select('MIN(price) AS min_price, MAX(price) AS max_price');
        $qb->facet('FACET category_id, FACET barnd_id');
        $qb->criteria("details.{$this->detail->id} = {$this->detail->valueId}");

        $results = $qb->getFeatures();

        $features = new DTO\Features();

        $features->price = new DTO\Range($results[0][0]['min_price'], $results[0][0]['max_price']);
        $features->categories = $this->getCategories($results[1]);
        $features->brands = $this->getBrands($results[2]);
        $features->total = $results[3][0]['total'];
        $features->availability = $this->getAvailability($results[4]);
        if ($this->getUserIsEmployee()) {
            $features->nofilled = $this->getNofilled(array_slice($results, 6));
        }

        return $features;
    }
}
