<?php 

namespace PricingBundle\Bus\Competitors\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\CompetitorTypeCode;
use AppBundle\Enum\ProductAvailabilityCode;
use AppBundle\Enum\ProductToCompetitorStatus;
use AppBundle\Enum\RepresentativeTypeCode;
use AppBundle\ORM\Query\DTORSM;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use PricingBundle\Component\CompetitorsComponent;
use PricingBundle\Entity\Product;

class GetRevisionQueryHandler extends MessageHandler
{
    const VIEW_MODE_LOOSING = 'loosing';
    const VIEW_MODE_ACTUAL = 'actual';
    const VIEW_MODE_OUTDATED = 'outdated';

    public function handle(GetRevisionQuery $query) : array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $limit = !empty($query->limit) ? intval($query->limit) : 50;
//        $limit = min(max(intval($query->limit), 50), 500);

        $categories = [];

        // Получить список товаров
        // Запрос количества товаров
        $qCount = $em->createQuery('
            SELECT
                COUNT(P.id) AS cnt
            FROM
                PricingBundle:ProductToCompetitor AS pc
                JOIN PricingBundle:Competitor AS C WITH C.id = pc.competitorId
                JOIN ContentBundle:BaseProduct AS bp WITH bp.id = pc.baseProductId
                JOIN PricingBundle:Product AS P WITH P.baseProductId = bp.id
                    AND '.(!empty($query->cityId) ? 'P.geoCityId = :city_id' : 'P.geoCityId IS NULL').'
                JOIN ContentBundle:CategoryPath AS cp WITH cp.id = bp.categoryId AND cp.plevel = 1
            WHERE
                '.(!empty($query->cityId) ? 'COALESCE ( pc.cityId, :city_id ) = :city_id' : 'pc.cityId IS NULL').'
                AND C.id = :competitor_id '.($query->viewMode === self::VIEW_MODE_LOOSING ? 'AND P.price > pc.competitorPrice' : '').'
                '.($query->viewMode === self::VIEW_MODE_LOOSING || $query->viewMode === self::VIEW_MODE_ACTUAL ?
                ' AND ( pc.serverResponse = 200 OR C.channel != :site AND pc.competitorPrice > 0 )
                        AND ( CASE WHEN C.channel IN ( :site, :pricelist )
                            THEN DATE_ADD(pc.priceTime, 7, \'DAY\')
                            ELSE DATE_ADD(pc.priceTime, 30, \'DAY\')
                        END ) >= CURRENT_TIMESTAMP()
                    ' :
                '').
            ($query->viewMode === self::VIEW_MODE_OUTDATED ?
                ' AND (C.channel = :site AND pc.serverResponse != 200
                        OR ( CASE WHEN C.channel IN ( :site, :pricelist )
                            THEN DATE_ADD(pc.priceTime, 7, \'DAY\')
                            ELSE DATE_ADD(pc.priceTime, 30, \'DAY\')
                        END ) < CURRENT_TIMESTAMP() )' :
                '').
            (!empty($query->categoryId) ? ' AND cp.pid = :category_id' : '').
            (!empty($query->createdFrom) ? ' AND pc.createdAt >= :created_from' : '').
            (!empty($query->createdTill) ? ' AND pc.createdAt <= :created_till' : '').
            (!empty($query->createdBy) ? ' AND pc.createdBy = :creator_id' : '').
            (!empty($query->channel === CompetitorTypeCode::SITE) ? ' AND C.channel = :site' : '').
            (!empty($query->channel === CompetitorTypeCode::RETAIL) ? ' AND C.channel = :retail' : '')
        );

        // Запрос самих товаров
        $q = $em->createQuery('
            SELECT
                NEW PricingBundle\Bus\Competitors\Query\DTO\RevisionProducts (
                    pc.baseProductId,
                    bp.categoryId,
                    bp.name,
                    pc.priceTime,
                    bp.supplierPrice,'. /* AS purchase_price, */ '
                    P.price,'. /* AS retail_price, */ '
                    pc.createdBy,
                    pc.link
                )
            FROM
                PricingBundle:ProductToCompetitor AS pc
                JOIN PricingBundle:Competitor AS C WITH C.id = pc.competitorId
                JOIN ContentBundle:BaseProduct AS bp WITH bp.id = pc.baseProductId
                JOIN PricingBundle:Product AS P WITH P.baseProductId = bp.id
                    AND '.(!empty($query->cityId) ? 'P.geoCityId = :city_id' : 'P.geoCityId IS NULL').'
                JOIN ContentBundle:CategoryPath AS cp WITH cp.id = bp.categoryId AND cp.plevel = 1
            WHERE
                '.(!empty($query->cityId) ? 'COALESCE ( pc.cityId, :city_id ) = :city_id' : 'pc.cityId IS NULL').'
                AND C.id = :competitor_id '.($query->viewMode === self::VIEW_MODE_LOOSING ? 'AND P.price > pc.competitorPrice' : '').'
                '.($query->viewMode === self::VIEW_MODE_LOOSING || $query->viewMode === self::VIEW_MODE_ACTUAL ?
                    ' AND ( pc.serverResponse = 200 OR C.channel != :site AND pc.competitorPrice > 0 )
                        AND ( CASE WHEN C.channel IN ( :site, :pricelist )
                            THEN DATE_ADD(pc.priceTime, 7, \'DAY\')
                            ELSE DATE_ADD(pc.priceTime, 30, \'DAY\')
                        END ) >= CURRENT_TIMESTAMP()
                    ' :
                    '').
                ($query->viewMode === self::VIEW_MODE_OUTDATED ?
                    ' AND (C.channel = :site AND pc.serverResponse != 200
                        OR ( CASE WHEN C.channel IN ( :site, :pricelist )
                            THEN DATE_ADD(pc.priceTime, 7, \'DAY\')
                            ELSE DATE_ADD(pc.priceTime, 30, \'DAY\')
                        END ) < CURRENT_TIMESTAMP() )' :
                    '').
                (!empty($query->categoryId) ? ' AND cp.pid = :category_id' : '').
                (!empty($query->createdFrom) ? ' AND pc.createdAt >= :created_from' : '').
                (!empty($query->createdTill) ? ' AND pc.createdAt <= :created_till' : '').
                (!empty($query->createdBy) ? ' AND pc.createdBy = :creator_id' : '').
                (!empty($query->channel === CompetitorTypeCode::SITE) ? ' AND C.channel = :site' : '').
                (!empty($query->channel === CompetitorTypeCode::RETAIL) ? ' AND C.channel = :retail' : '')
        );

        $q->setParameter('site', CompetitorTypeCode::SITE);
        $qCount->setParameter('site', CompetitorTypeCode::SITE);
        $q->setParameter('pricelist', CompetitorTypeCode::PRICELIST);
        $qCount->setParameter('pricelist', CompetitorTypeCode::PRICELIST);
        if ($query->channel === CompetitorTypeCode::RETAIL) {
            $q->setParameter('retail', CompetitorTypeCode::RETAIL);
            $qCount->setParameter('retail', CompetitorTypeCode::RETAIL);
        }
        $q->setParameter('competitor_id', $query->id);
        $qCount->setParameter('competitor_id', $query->id);
        if (!empty($query->cityId)) {
            $q->setParameter('city_id', $query->cityId);
            $qCount->setParameter('city_id', $query->cityId);
        }
        if (!empty($query->categoryId)) {
            $q->setParameter('category_id', $query->categoryId);
            $qCount->setParameter('category_id', $query->categoryId);
        }
        if (!empty($query->createdBy)) {
            $q->setParameter('creator_id', $query->createdBy);
            $qCount->setParameter('creator_id', $query->createdBy);
        }
        if (!empty($query->createdFrom)) {
            $q->setParameter('created_from', $query->createdFrom);
            $qCount->setParameter('created_from', $query->createdFrom);
        }
        if (!empty($query->createdTill)) {
            $q->setParameter('created_till', $query->createdTill);
            $qCount->setParameter('created_till', $query->createdTill);
        }

        $q->setMaxResults($limit);
        if (!empty($query->page))
            $q->setFirstResult((max(intval($query->page), 1) - 1) * $limit);

        $products = $q->getResult();

        $productsCount = $qCount->getArrayResult();
        $productsCount = $productsCount[0]['cnt'];

        $values = array_map(function ($p) { return $p->categoryId; }, $products);

        // Получаем список категорий
        if ($values) {
            $q = $em->createQuery('
                SELECT
                    NEW PricingBundle\Bus\Competitors\Query\DTO\RevisionCategories (
                        C.id,
                        C.name
                    )
                FROM
                    ContentBundle:CategoryPath AS cp
                    JOIN ContentBundle:Category AS C WITH cp.pid = C.id
                WHERE
                    cp.id IN (:categoryIds) AND cp.plevel = 1
                GROUP BY
                    C.id
                ORDER BY
                    C.name
            ');
            $q->setParameter('categoryIds', $values);
            $categories = $q->getResult();

            $q = $em->createQuery('
                SELECT
                    NEW PricingBundle\Bus\Competitors\Query\DTO\RevisionCategories (
                        C.id,
                        C.name,
                        cp.pid
                    )
                FROM
                    ContentBundle:CategoryPath AS cp
                    JOIN ContentBundle:Category AS C WITH cp.id = C.id
                WHERE
                    cp.id IN (:categoryIds) AND cp.plevel = 1
                GROUP BY
                    C.id,
                    cp.pid
                ORDER BY
                    cp.pid,
                    C.name
            ');
            $q->setParameter('categoryIds', $values);
            $categories = array_merge($categories, $q->getResult());

            $baseCategory = new \PricingBundle\Bus\Competitors\Query\DTO\RevisionCategories();
            array_unshift($categories, $baseCategory);

            $children = [];
            foreach ($categories as &$category) {
                if (empty($category->pid)) {
                    $category->pid = 0;
                }

                foreach ($products as $product) {
                    if ($product->categoryId == $category->id) {
                        $category->productsIds[$product->id] = $product->id;
                    }
                }

                $category->productsIds = !empty($category->productsIds) ? array_values($category->productsIds) : [];

                if ($category->id > 0) {
                    $children[$category->pid][$category->id] = $category->id;
                }
            }

            foreach ($categories as &$category) {
                if (!empty($children[$category->id])) {
                    $category->children = array_values($children[$category->id]);
                } else {
                    $category->children = [];
                }
            }
        }

        $values = array_map(function ($p) { return $p->id; }, $products);

        // Получаем сверку по конкурентам
        if ($values) {
            $q = $em->createQuery('
                SELECT
                    pc.baseProductId,
                    pc.competitorId,
                    pc.link,
                    CASE WHEN
                            (CASE WHEN C.channel IN ( :site, :pricelist )
                                THEN DATE_ADD(pc.priceTime, 7, \'DAY\')
                                ELSE DATE_ADD(pc.priceTime, 30, \'DAY\') 
                            END) >= CURRENT_TIMESTAMP()
                            AND ( pc.serverResponse = 200 OR C.channel != :site AND pc.competitorPrice > 0 )
                        THEN pc.competitorPrice
                        ELSE 0 
                    END AS competitorPrice 
                FROM
                    PricingBundle:ProductToCompetitor AS pc
                    JOIN PricingBundle:Competitor AS C WITH C.id = pc.competitorId 
                WHERE
                    pc.baseProductId IN (:baseProductIds)
                    ' . (!empty($query->cityId) ? 'AND COALESCE ( pc.cityId, :city_id ) = :city_id' : 'AND pc.cityId IS NULL')
            );

            $q->setParameter('baseProductIds', $values);
            $q->setParameter('site', CompetitorTypeCode::SITE);
            $q->setParameter('pricelist', CompetitorTypeCode::PRICELIST);
            if (!empty($query->cityId))
                $q->setParameter('city_id', $query->cityId);

            $list = $q->getArrayResult();

            $competitors = [];
            foreach ($list as $item) {
                $competitors[$item['baseProductId']][$item['competitorId']] = [
                    'link' => $item['link'],
                    'competitorPrice' => $item['competitorPrice'],
                ];
            }

            foreach ($products as &$product) {
                $product->competitors = isset($competitors[$product->id]) ? $competitors[$product->id] : [];
            }
        }

        return ['products' => $products, 'categories' => $categories, 'total' => $productsCount];
    }
}