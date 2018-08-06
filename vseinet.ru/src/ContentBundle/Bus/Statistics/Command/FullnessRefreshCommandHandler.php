<?php 

namespace ContentBundle\Bus\Statistics\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\ProductAvailabilityCode;

class FullnessRefreshCommandHandler extends MessageHandler
{
    const EXCLUDE_CATEGORIES = [7562, 33536];

    public function handle(FullnessRefreshCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery("
            DELETE FROM ContentBundle:Fullness f 
            WHERE f.subject = :subject 
        ");
        $q->setParameter('subject', $command->subject);
        $q->execute();

        $active = "(bp.supplier_availability_code > '".ProductAvailabilityCode::OUT_OF_STOCK."' OR bp.free_reserve > 0)";

        $q = $em->createQuery("
            SELECT cp.id 
            FROM ContentBundle:CategoryPath cp
            WHERE cp.pid IN (:excludeCategories)
        ");
        $q->setParameter('excludeCategories', static::EXCLUDE_CATEGORIES);
        $exclude = implode(',', $q->getResult('ListHydrator'));

        $q = $em->createQuery("
            SELECT 
                MAX(cp.level)
            FROM ContentBundle:CategoryPath cp 
        ");
        $level = $q->getSingleScalarResult();

        switch ($command->subject) {
            case 'images':
                $this->calculateFillnessByImages($active, $exclude);
                break;

            case 'descriptions':
                $this->calculateFillnessByDescriptions($active, $exclude);
                break;

            case 'details':
                $this->calculateFillnessByDetails($active, $exclude);
                break;

            case 'brands':
                $this->calculateFillnessByBrands($active, $exclude);
                break;
        }

        $stmt = $em->getConnection()->prepare("
            INSERT INTO content_fullness (
                category_id,
                subject,
                total,
                active,
                count,
                count_from_parser,
                count_active,
                count_active_from_parser
            )
            (
                SELECT 
                    c.pid,
                    :subject,
                    SUM(fs.total),
                    SUM(fs.active),
                    SUM(fs.count),
                    SUM(fs.count_from_parser),
                    SUM(fs.count_active),
                    SUM(fs.count_active_from_parser)
                FROM category c
                INNER JOIN category_path cp ON cp.id = c.id AND cp.pid = c.id
                INNER JOIN content_fillness f ON f.category_id = c.id
                WHERE cp.level = :level
                GROUP BY c.pid
            )
            ON CONFLICT (category_id, subject)
            DO UPDATE SET
                total = content_fullness.total + EXCLUDED.total,
                active = content_fullness.active + EXCLUDED.active,
                count = content_fullness.count + EXCLUDED.count,
                count_from_parser = content_fullness.count_from_parser + EXCLUDED.count_from_parser,
                count_active = content_fullness.count_active + EXCLUDED.count_active,
                count_active_from_parser = content_fullness.count_active_from_parser + EXCLUDED.count_active_from_parser
        ");
        while ($level) {
            $stmt->execute(['subject' => $command->subject, 'level' => $level]);
            $level--;
        }

        $stmt = $em->getConnection()->prepare("
            UPDATE content_fullness
            SET percent_fullness = (CASE WHEN total = 0 THEN 0 ELSE ROUND(100 * count::numeric / total::numeric, 2) END),
                active_percent_fullness = (CASE WHEN active = 0 THEN 0 ELSE ROUND(100 * count_active::numeric / active::numeric, 2) END),
                updated_at = NOW()
            WHERE subject = :subject
        ");
        $stmt->execute(['subject' => $command->subject]);

        $this->get('old_sound_rabbit_mq.notify.front_producer')->publish(json_encode([
            'type' => 'content:fullness:refresh',
            'data' => [
                'subject' => $command->subject,
            ],
        ]));
    }

    protected function calculateFullnessByImages($active, $exclude)
    {
        $stmt = $this->getDoctrine()->getConnection()->prepare("
             INSERT INTO content_fullness (
                category_id,
                subject,
                total,
                active,
                count,
                count_from_parser,
                count_active,
                count_active_from_parser 
            )
            SELECT 
                bp.category_id,
                'images',
                COUNT(bp.id),
                SUM(CASE WHEN {$active} THEN 1 ELSE 0 END),
                SUM(CASE WHEN bpi.id IS NOT NULL THEN 1 ELSE 0 END),
                SUM(CASE WHEN bpi.parser_image_id IS NOT NULL THEN 1 ELSE 0 END),
                SUM(CASE WHEN bpi.id IS NOT NULL AND {$active} THEN 1 ELSE 0 END),
                SUM(CASE WHEN bpi.parser_image_id IS NOT NULL AND {$active} THEN 1 ELSE 0 END)
            FROM base_product bp
            LEFT OUTER JOIN base_product_image bpi ON bpi.base_product_id = bp.id AND bpi.sort_order = 1
            WHERE bp.category_id NOT IN ({$exclude})
            GROUP BY bp.category_id
        ");
        $stmt->execute();
    }

    protected function calculateFullnessByDescriptions($active, $exclude)
    {
        $stmt = $this->getDoctrine()->getManager()->getConnection()->prepare("
             INSERT INTO content_fullness (
                category_id,
                subject,
                total,
                active,
                count,
                count_from_parser,
                count_active,
                count_active_from_parser 
            )
            SELECT 
                bp.category_id,
                'descriptions',
                COUNT(bp.id),
                SUM(CASE WHEN {$active} THEN 1 ELSE 0 END),
                SUM(CASE WHEN bpd.base_product_id IS NOT NULL THEN 1 ELSE 0 END),
                SUM(CASE WHEN bpd.parser_product_id IS NOT NULL THEN 1 ELSE 0 END),
                SUM(CASE WHEN bpd.base_product_id IS NOT NULL AND {$active} THEN 1 ELSE 0 END),
                SUM(CASE WHEN bpd.parser_product_id IS NOT NULL AND {$active} THEN 1 ELSE 0 END)
            FROM base_product bp
            LEFT OUTER JOIN base_product_description bpd ON bpd.base_product_id = bp.id 
            WHERE bp.category_id NOT IN ({$exclude})
            GROUP BY bp.category_id
        ");
        $stmt->execute();
    }

    /**
     * @todo: сделать подсчет кол-ва товаров с характеристиками из парсера
     */
    protected function calculateFullnessByDetails($active, $exclude)
    {
        $stmt = $this->getDoctrine()->getManager()->getConnection()->prepare("
             INSERT INTO content_fillness (
                category_id,
                subject,
                total,
                active,
                count,
                count_from_parser,
                count_active,
                count_active_from_parser 
            )
            SELECT 
                bp.category_id,
                'details',
                COUNT(bp.id),
                SUM(CASE WHEN {$active} THEN 1 ELSE 0 END),
                SUM(
                    CASE WHEN EXISTS (
                        SELECT 1 
                        FROM content_detail_to_product d2p 
                        WHERE d2p.base_product_id = bp.id    
                    ) THEN 1 ELSE 0 END
                ),
                0,
                SUM(
                    CASE WHEN EXSIST (
                        SELECT 1
                        FROM content_detail_to_product d2p 
                        WHERE d2p.base_product_id = bp.id AND {$active} 
                    ) THEN 1 ELSE 0 END
                ),
                0
            FROM base_product bp
            WHERE bp.categoryId NOT IN ({$exclude})
            GROUP BY bp.category_id
        ");
        $stmt->execute();
    }

    /**
     * @todo: сделать подсчет кол-ва товаров с брендами из парсера
     */
    protected function calculateFullnessByBrands($active, $exclude)
    {
        $stmt = $this->getDoctrine()->getManager()->getConnection()->prepare("
             INSERT INTO content_fullness (
                category_id,
                subject,
                total,
                active,
                count,
                count_from_parser,
                count_active,
                count_active_from_parser 
            )
            SELECT 
                bp.category_id,
                'brands',
                COUNT(bp.id),
                SUM(CASE WHEN {$active} THEN 1 ELSE 0 END),
                SUM(CASE WHEN bp.brand_id IS NOT NULL THEN 1 ELSE 0 END),
                0,
                SUM(CASE WHEN bp.brand_id IS NOT NULL AND {$active} THEN 1 ELSE 0 END),
                0
            FROM base_product bp
            WHERE bp.category_id NOT IN ({$exclude})
            GROUP BY bp.category_id
        ");
        $stmt->execute();
    }
}