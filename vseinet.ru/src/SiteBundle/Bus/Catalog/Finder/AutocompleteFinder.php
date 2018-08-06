<?php 

namespace SiteBundle\Bus\Catalog\Finder;

use AppBundle\Container\ContainerAware;
use SiteBundle\Bus\Catalog\Enum\Availability;
use SiteBundle\Bus\Catalog\Query\DTO\Filter;
use SiteBundle\Bus\Catalog\Query\DTO\Autocomplete;

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

    public function getCityId()
    {
        return $this->get('city.identity')->getId();
    }

    public function getResult()
    {
        $em = $this->getDoctrine()->getManager();
        $result = [];

        $categoryIds = $this->getCategoryIds();
        if (!empty($categoryIds)) {
            $q = $em->createQuery("
                SELECT 
                    NEW SiteBundle\Bus\Catalog\Query\DTO\Autocomplete\Category (
                        c.id,
                        c.name,
                        c.pid 
                    )
                FROM ContentBundle:Category c 
                INNER JOIN ContentBundle:CategoryPath cp WITH cp.pid = c.id 
                WHERE c.aliasForId IS NULL AND cp.plevel > 0 AND cp.id IN (:ids)
            ");
            $q->setParameter('ids', $categoryIds);
            $categories = $q->getResult('IndexByHydrator');

            foreach ($categoryIds as $id) {
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

        $products = $this->getProducts();
        if (!empty($products)) {
            foreach ($products as $product) {
                $result[] = new Autocomplete\Product($product['id'], $product['name']);
            }
        }

        return array_values($result);
    }

    protected function getCategoryIds()
    {
        $query = "
            SELECT id, WEIGHT() AS weight
            FROM category 
            WHERE {$this->getMainCriteria()}
            ORDER BY weight DESC, rating DESC
            LIMIT ".self::COUNT_CATEGORIES." 
            OPTION ranker=expr('sum((4*lcs+2*(min_hit_pos==1)+exact_hit)*user_weight)*1000+bm25');                
        ";
        $results = $this->container->get('sphinxql')->execute($query);

        return array_map(function($row) { return intval($row['id']); }, $results[0]);
    }

    protected function getProducts()
    {
        $query = "
            SELECT id, name, WEIGHT() AS weight 
            FROM base_product 
            WHERE {$this->getMainCriteria()} AND {$this->getCriteriaAlive()} AND {$this->getCriteriaAvailability()}
            ORDER BY availability.{$this->getCityId()} ASC, weight DESC, rating DESC
            LIMIT ".self::COUNT_PRODUCTS."
            OPTION ranker=expr('sum((4*lcs+2*(min_hit_pos==1)+exact_hit)*user_weight)*1000+bm25');
        ";
        $results = $this->container->get('sphinxql')->execute($query);

        return $results[0];
    }

    protected function getMainCriteria()
    {
        return "MATCH('".$this->get('sphinxql')->escapeMatch($this->data->q)."')";
    }

    protected function getCriteriaAlive(): string
    {
        return "killbill = 0 AND is_forbidden = 0";
    }

    protected function getCriteriaAvailability(): string
    {
        $availability = $this->get('user.identity')->isEmployee() ? Availability::FOR_ALL_TIME : Availability::ACTIVE;

        return "availability.{$this->getCityId()} <= {$availability}";
    }
}
