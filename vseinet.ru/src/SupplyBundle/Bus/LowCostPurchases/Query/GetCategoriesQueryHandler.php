<?php 

namespace SupplyBundle\Bus\LowCostPurchases\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\ProductAvailabilityCode;
use Doctrine\ORM\Query\ResultSetMapping;

class GetCategoriesQueryHandler extends MessageHandler
{
    const WARNING_PERCENT = 50;

    /**
     * @var \Doctrine\ORM\EntityManager $em
     */
    public $em;

    public function handle(GetCategoriesQuery $query) : array
    {
        $this->em = $this->getDoctrine()->getManager();

        return $this->camelizeKeys($this->_getCategories());
    }

    private function _getCategories()
    {
        $data = ['list' => [], 'totalCount' => 0,];

        $categoryIDs = $this->_getAllCategoryIDs();

        if (!empty($categoryIDs)) {
            $q = $this->em->createQuery('
                SELECT
                    c.id,
                    c.name,
                    c2.id AS pid,
                    c2.name AS pname
                FROM
                    ContentBundle:Category AS c
                    INNER JOIN ContentBundle:CategoryPath AS cp WITH cp.id = c.id
                    INNER JOIN ContentBundle:Category AS c2 WITH cp.pid = c2.id
                        AND c2.pid = 0
                WHERE
                    c.id IN ( :ids )
                ORDER BY
                    c.name ASC
            ');

            $q->setParameter('ids', array_keys($categoryIDs));

            $categories = $q->getArrayResult();

            foreach ($categories as $categoryData) {
                $categoryID = $categoryData['id'];
                $categoryData['count'] = isset($categoryIDs[$categoryID]) ? $categoryIDs[$categoryID]['cnt'] : 0;
                $categoryData['maxPrc'] = isset($categoryIDs[$categoryID]) ? $categoryIDs[$categoryID]['maxPrc'] : 0;

                if (!isset($data['list'][$categoryData['pid']])) {
                    $data['list'][$categoryData['pid']] = new DTO\Categories(
                        $categoryData['pid'],
                        $categoryData['pname'],
                        false,
                        NULL,
                        NULL,
                        []
                    );
                }

                if (intval($categoryData['maxPrc']) > self::WARNING_PERCENT) {
                    $data['list'][$categoryData['pid']]->isWarning = true;
                }

                $data['list'][$categoryData['pid']]->childrens[$categoryID] = new DTO\Categories(
                    $categoryID,
                    $categoryData['name'],
                    $data['list'][$categoryData['pid']]->isWarning,
                    $categoryData['count'],
                    $categoryData['maxPrc']
                );

                $data['totalCount'] += $categoryData['count'];
            }
        }

        $data['list'] = array_values($data['list']);

        return $data;
    }

    private function _getAllCategoryIDs()
    {
        $result = [];
        $q = $this->em->createQuery('
            SELECT
                DISTINCT bp.categoryId,
                MAX(((sp.price - bp.supplierPrice)/((sp.price + bp.supplierPrice)/2)*100)) as maxPrc,
                COUNT(bp.id) as cnt
            FROM
                ContentBundle:BaseProduct AS bp
                INNER JOIN ContentBundle:Category AS c WITH bp.categoryId = c.id
                INNER JOIN PricingBundle:Product AS p WITH bp.id = p.baseProductId
                INNER JOIN SupplyBundle:SupplierProduct sp WITH sp.baseProductId = bp.id
                    AND sp.price > 0
                        AND sp.availabilityCode = :available
                LEFT JOIN SupplyBundle:SupplierProduct sp2 WITH sp2.baseProductId = bp.id
                    AND sp2.price > 0
                    AND sp2.availabilityCode = :available
                    AND (sp.price > sp2.price OR sp.price = sp2.price AND sp.id < sp2.id)
            WHERE
                sp2.id IS NULL AND
                ((sp.price - bp.supplierPrice)/((sp.price + bp.supplierPrice)/2)*100) > 50
            GROUP BY bp.categoryId
        ');

        $q->setParameter('available', ProductAvailabilityCode::AVAILABLE);

        $rows = $q->getArrayResult();

        foreach ($rows as $row) {
            $result[$row['categoryId']] = $row;
        }

        return $result;
    }
}