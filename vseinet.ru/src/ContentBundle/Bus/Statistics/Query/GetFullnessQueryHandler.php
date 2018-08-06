<?php 

namespace ContentBundle\Bus\Statistics\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Category;

class GetFullnessQueryHandler extends MessageHandler
{
    public function handle(GetFullnessQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository(Category::class)->find($query->categoryId);
        if (!$category instanceof Category) {
            throw new NotFoundHttpException(sprintf('Категория %d не найдена', $query->categoryId));
        }

        switch ($query->filter) {
            case 'all':
                $fieldCount = 'countActive';
                $fieldCountFromParser = 'countActiveFromParser';
                $fieldPercentFullness = 'activePercentFullness';
                break;

            case 'active':
                $fieldCount = 'count';
                $fieldCountFromParser = 'countFromParser';
                $fieldPercentFullness = 'percentFullness';
                break;
        }

        switch ($query->sort) {
            case 'alphabetically':
                $sort = "c.name ASC";
                break;

            case 'fillness-asc':
                $sort = "f.{$fieldPercentFullness} ASC";
                break;

            case 'fillness-desc':
                $sort = "f.{$fieldPercentFullness} DESC";
                break;
        }

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\Statistics\Query\DTO\Fullness (
                    c.id,
                    c.name,
                    f.{$fieldCount},
                    f.{$fieldCountFromParser},
                    f.{$fieldPercentFullness},
                    CASE WHEN EXISTS (
                        SELECT 1 
                        FROM ContentBundle:Category cc 
                        WHERE cc.pid = c.id
                    ) THEN false ELSE true END
                )
            FROM ContentBundle:Fullness f
            INNER JOIN ContentBundle:Category c WITH c.id = f.categoryId
            WHERE c.pid = :categoryId AND f.subject = :subject AND f.{$fieldPercentFullness} < :percentFullness
            ORDER BY {$sort}
        ");
        $q->setParameter('categoryId', $category->getId());
        $q->setParameter('subject', $query->subject);
        $q->setParameter('percentFullness', $query->percentFullness);

        return $q->getArrayResult();
    }
}