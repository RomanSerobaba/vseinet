<?php 

namespace AppBundle\Bus\Catalog\Finder\Block;

use Doctrine\ORM\EntityManager;
use AppBundle\Bus\Catalog\Query\DTO\Filter\Brand;

class Brands
{
    const COUNT_SHOW_BRANDS = 50;
    const COUNT_TOP_BRANDS = 7;

    /**
     * @return array<Brand>
     */
    public static function build(array $brandId2count, EntityManager $em): array 
    {
        if (empty($brandId2count)) {
            return null;
        }

        arsort($brandId2count);

        if (self::COUNT_SHOW_BRANDS < count($brandId2count)) {
            $otherBrandId2Count = array_slice($brandId2count, self::COUNT_SHOW_BRANDS, null, true);
            $brandId2count = array_slice($brandId2count, 0, self::COUNT_SHOW_BRANDS, true);
        }

        $q = $em->createQuery("
            SELECT 
                NEW AppBundle\Bus\Catalog\Query\DTO\Filter\Brand (
                    b.id, 
                    b.name
                ),
                CASE WHEN b.id > 0 THEN 1 ELSE 2 END AS HIDDEN ORD
            FROM AppBundle:Brand AS b 
            WHERE b.id IN (:ids)
            ORDER BY ORD, b.name 
        ");
        $q->setParameter('ids', array_keys($brandId2count));
        $brands = $q->getResult('IndexByHydrator');
        
        foreach ($brandId2count as $id => $count) {
            $brands[$id]->countProducts = $count;
        }
        $brandId2count = array_slice($brandId2count, 0, self::COUNT_TOP_BRANDS, true);
        foreach ($brandId2count as $id => $count) {
            $brands[$id]->isTop = 1;
        }

        if (!empty($otherBrandId2Count)) {
            $brands[-1] = new Brand(-1, 'Прочие');
            $brands[-1]->countProducts = array_sum($otherBrandId2Count);
            $brands[-1]->includeIds = array_keys($otherBrandId2Count);
        }

        return $brands;
    }
}
