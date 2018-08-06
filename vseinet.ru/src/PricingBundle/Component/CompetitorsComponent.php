<?php

namespace PricingBundle\Component;

use AppBundle\Enum\ProductAvailabilityCode;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use PricingBundle\Bus\Competitors\Query\GetIndexQuery;
use PricingBundle\Bus\Competitors\Query\GetStatsQueryHandler;

class CompetitorsComponent
{
    /**
     * Entity Manager
     *
     * @var EntityManager
     */
    private $_em;

    /**
     * @param EntityManager $em
     */
    public function setEm(EntityManager $em) : void
    {
        $this->_em = $em;
    }

    /**
     * @return EntityManager
     */
    public function getEm() : EntityManager
    {
        return $this->_em;
    }

    public function __construct(EntityManager $em)
    {
        $this->_em = $em;
    }

    private $_managers = [];
    private $_competitors = [];
    private $_competitorsNum = 0;
    private $_competitorKeys = [
        'id',
        'name',
        'price',
        'created',
        'checked', //date of previos check
        'mid', //manager ID
        'link',
    ];
    private $_competitorMap = [];

    public function getIndexList(GetIndexQuery $query) : array
    {
        $typeSQL = '';
        if (!empty($query->type)) {
            $typeSQL = 'AND ptc.competitor_price + 1000 < p.price + p.delivery_tax';
        }

        $q = $this->getEm()->createNativeQuery('
            SELECT
                c.id,
                c.name 
            FROM
                category c
                INNER JOIN category_path cp ON cp.pid = c.id
                INNER JOIN base_product bp ON bp.category_id = cp.id
                INNER JOIN product p ON p.base_product_id = bp.id
                INNER JOIN product_to_competitor ptc ON ptc.base_product_id = p.base_product_id
                INNER JOIN competitor cm ON ptc.competitor_id = cm.id 
            WHERE
                cm.is_active = TRUE 
                AND ptc.competitor_price > 0 
                AND p.product_availability_code <> :out_of_stock
                AND EXTRACT(DAY FROM NOW() - ptc.price_time) <= :day_diff
                '.$typeSQL.'
            GROUP BY
                c.id
        ', new ResultSetMapping());
        $q->setParameter('out_of_stock', ProductAvailabilityCode::OUT_OF_STOCK);
        $q->setParameter('day_diff', GetStatsQueryHandler::DAY_DIFF);

        $categories = [];
        $rows = $q->getResult('ListAssocHydrator');
        foreach ($rows as $row) {
            $categories[$row['id']] = $row['name'];
        }
        unset($rows);

        $q = $this->getEm()->createNativeQuery("
            SELECT DISTINCT
                u.id,
                CONCAT_WS( ' ', person.firstname, person.lastname ) AS name 
            FROM
                \"user\" u
                INNER JOIN product_to_competitor ptc ON u.id = ptc.created_by AND ptc.created_by IS NOT NULL AND ptc.created_by > 0
                INNER JOIN product ON ptc.base_product_id = product.base_product_id
                INNER JOIN competitor ON ptc.competitor_id = competitor.id 
                INNER JOIN person ON u.person_id = person.id 
            WHERE
                competitor.is_active = TRUE 
                AND product.product_availability_code <> :out_of_stock
        ", new ResultSetMapping());
        $q->setParameter('out_of_stock', ProductAvailabilityCode::OUT_OF_STOCK);

        foreach($q->getResult('ListAssocHydrator') as $item) {
            $this->_managers[$item['id']] = $item['name'];
        }

        $q = $this->getEm()->createNativeQuery('
            SELECT
                competitor.id,
                competitor.name
            FROM
                competitor
            WHERE
                is_active = TRUE
        ', new ResultSetMapping());
        $rows = $q->getResult('ListAssocHydrator');

        foreach ($rows as $row) {
            $this->_competitors[$row['id']] = $row['name'];
        }
        unset($rows);

        $this->_competitorsNum = count($this->_competitors);
        $this->_competitorMap = array_flip(array_keys($this->_competitors));

        $data = [];
        $rawItems = $this->_fetchItems($query);

        foreach($rawItems as $item) {
            $this->_intoTheDepths($data, $item);
        }

        return ['data' => $data, 'categories' => $categories, 'managers' => $this->_managers, 'competitors' => $this->_competitors,];
    }

    /**
     * @param GetIndexQuery $query
     *
     * @return array
     */
    private function _fetchItems(GetIndexQuery $query) : array
    {
        $typeSQL = $categotySQL = '';
        if (!empty($query->type)) {
            $typeSQL = 'AND ptc.price + 1000 < product.price + product.delivery_tax';
        }
        if (!empty($query->categoryId)) {
            $categotySQL = sprintf('AND cp1.pid = %u', $query->categoryId);
        }

        $q = $this->getEm()->createNativeQuery("
            SELECT
                product.id AS id,
                base_product.id AS code,
                string_agg(DISTINCT category.id || '', ',') AS category_ids,
                base_product.name AS product,
                CASE WHEN get_base_product_reserve_price(base_product.id, ptc.geo_city_id) > base_product.supplier_price 
                    THEN get_base_product_reserve_price(base_product.id, ptc.geo_city_id) 
                    ELSE base_product.supplier_price 
                END AS contractor_price,
                product.price + product.delivery_tax AS price,
                product.delivery_tax,
                string_agg(
                    DISTINCT CONCAT_WS(
                        ',',
                        competitor.id,
                        competitor.name,
                        CASE WHEN ptc.competitor_price IS NULL THEN 0 ELSE ptc.competitor_price END,
                        COALESCE(ptc.created_at, DATE '0001-01-01'),
                        COALESCE(ptc.price_time, DATE '0001-01-01'),
                        CASE WHEN ptc.created_by IS NULL THEN 0 ELSE ptc.created_by END,
                        CASE WHEN ptc.link IS NULL THEN '' ELSE ptc.link END
                    ), '|' 
                ) AS competitors 
            FROM
                base_product
                INNER JOIN category_path cat_path ON cat_path.id = base_product.category_id
                INNER JOIN category_path cp1 ON cp1.id = base_product.category_id
                INNER JOIN category ON category.id IN (cat_path.pid)
                INNER JOIN product ON base_product.id = product.base_product_id
                INNER JOIN product_to_competitor ptc ON ptc.base_product_id = product.base_product_id 
            	{$typeSQL}
                INNER JOIN competitor ON ptc.competitor_id = competitor.id
            WHERE
                competitor.is_active = TRUE 
                AND ptc.competitor_price > 0 
                AND product.product_availability_code <> :out_of_stock
                AND EXTRACT(DAY FROM NOW() - ptc.price_time) <= :day_diff
            	{$categotySQL}
            GROUP BY
                product.id,
                base_product.id,
                ptc.geo_city_id
        ", new ResultSetMapping());
        $q->setParameter('out_of_stock', ProductAvailabilityCode::OUT_OF_STOCK);
        $q->setParameter('day_diff', GetStatsQueryHandler::DAY_DIFF);

        return $q->getResult('ListAssocHydrator');
    }

    /**
     * @param array $item
     *
     * @return array
     */
    private function _prepeareItem(array $item) : array
    {
        $result = [
            'id' => $item['id'],
            'code' => $item['code'],
            'product' => $item['product'],
            'contractor_price' => $item['contractor_price'],
            'delta' => round(100-$item['contractor_price']/$item['price']*100, 0),
            'price' => $item['price'],
            'delivery_tax' => $item['delivery_tax'],
            'competitors' => array_fill(0, $this->_competitorsNum, null),
            'category_ids' => $item['category_ids'],
        ];

        foreach(explode('|', $item['competitors']) as $competitorStr) {
            $r = explode(',', $competitorStr);
            if (count($this->_competitorKeys) === count($r)) {
                $competitor = array_combine($this->_competitorKeys , $r);

                $result['competitors'][$competitor['id']] = [
                    'price' => $competitor['price'],
                    'link' => $competitor['link'],
                    'manager' => !empty($competitor['mid']) ? $this->_managers[$competitor['mid']] : false,
                    'created' => str_replace(' ', '&nbsp;', $competitor['created']),
                    'checked' => str_replace(' ', '&nbsp;', $competitor['checked'])
                ];
            }
        }

        return $result;
    }

    /**
     * @param $data
     * @param $item
     *
     * @return array
     */
    private function _intoTheDepths(&$data, $item)
    {
        $categoryIds = explode(',', $item['category_ids']);
        $pointer = &$data;

        foreach($categoryIds as $categoryId) {
            if (empty($pointer[$categoryId])) {
                $pointer[$categoryId] = [];
            }
            $pointer = &$pointer[$categoryId];
        }
        $pointer['items'][] = $this->_prepeareItem($item);

        return $categoryIds;
    }
}