<?php 

namespace AppBundle\Bus\Catalog\Finder;

use AppBundle\Container\ContainerAware;
use AppBundle\Bus\Catalog\Enum\Availability;
use AppBundle\Bus\Catalog\Query\DTO\Filter;
use AppBundle\Bus\Catalog\Query\DTO\Autocomplete;
use AppBundle\Entity\BaseProduct;

class AutocompleteFinder extends ContainerAware
{
    const COUNT_CATEGORIES = 3;
    const COUNT_PRODUCTS = 10;

    /**
     * @var Filter\Data
     */
    protected $data;


    public function setData(Filter\Data $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getResult(): array
    {
        $em = $this->getDoctrine()->getManager();
        $result = [];

        $categoryIds = $this->getCategoryIds();
        if (!empty($categoryIds)) {
            $q = $em->createQuery("
                SELECT 
                    NEW AppBundle\Bus\Catalog\Query\DTO\Autocomplete\Category (
                        c.id,
                        c.name,
                        c.pid 
                    )
                FROM AppBundle:Category c 
                INNER JOIN AppBundle:CategoryPath cp WITH cp.pid = c.id 
                WHERE c.aliasForId IS NULL AND cp.plevel > 0 AND cp.id IN (:ids)
            ");
            $q->setParameter('ids', $categoryIds);
            $categories = $q->getResult('IndexByHydrator');

            foreach ($categoryIds as $id) {
                if (!empty($categories[$id])) { // кастыль
                    $pid = $categories[$id]->pid;
                    $breadcrumbs = [];
                    while ($pid) {
                        $breadcrumbs[] = $categories[$pid];
                        $pid = $categories[$pid]->pid; 
                    }
                    $result[$id] = clone $categories[$id];
                    $result[$id]->breadcrumbs = array_reverse($breadcrumbs);
                }
            }
        }

        if(is_numeric($this->data->q)) {
            $product = $em->getRepository(BaseProduct::class)->find($this->data->q);
            if ($product instanceof BaseProduct) {
                $result[] = new Autocomplete\Product($product->getId(), $product->getName());
            }
        }

        $products = $this->getProducts();
        if (!empty($products)) {
            foreach ($products as $product) {
                $result[] = new Autocomplete\Product($product['id'], $product['name']);
            }
        }

        return array_values($result);
    }

    protected function getCategoryIds(): array
    {
        $query = "
            SELECT id, WEIGHT() AS weight
            FROM category 
            WHERE {$this->getMainCriteria()}
            ORDER BY weight DESC, rating DESC
            LIMIT ".self::COUNT_CATEGORIES." 
            OPTION ranker=expr('sum((4*lcs+2*(min_hit_pos==1)+exact_hit)*user_weight)*1000+bm25');                
        ";
        $results = $this->get('sphinxql')->execute($query);

        return array_map(function($row) { return intval($row['id']); }, $results[0]);
    }

    protected function getProducts(): array
    {
        $query = "
            SELECT id, name, WEIGHT() AS weight 
            FROM base_product 
            WHERE {$this->getMainCriteria()} AND {$this->getCriteriaAlive()} AND {$this->getCriteriaAvailability()}
            ORDER BY availability.{$this->getGeoCity()->getRealId()} ASC, weight DESC, rating DESC
            LIMIT ".self::COUNT_PRODUCTS."
            OPTION ranker=expr('sum((4*lcs+2*(min_hit_pos==1)+exact_hit)*user_weight)*1000+bm25');
        ";
        $results = $this->get('sphinxql')->execute($query);

        return $results[0];
    }

    protected function getMainCriteria(): string 
    {
        return "MATCH('".$this->get('sphinxql')->escapeMatch($this->data->q)."')";
    }

    protected function getCriteriaAlive(): string
    {
        return "killbill = 0 AND is_forbidden = 0";
    }

    protected function getCriteriaAvailability(): string
    {
        $availability = $this->getUserIsEmployee() ? Availability::FOR_ALL_TIME : Availability::ACTIVE;

        return "availability.{$this->getGeoCity()->getRealId()} <= {$availability}";
    }
}
