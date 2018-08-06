<?php 

namespace ContentBundle\Bus\Template\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Category;

class GetCategoriesQueryHandler extends MessageHandler 
{
    public function handle(GetCategoriesQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $parent = $em->getRepository(Category::class)->find($query->pid);
        if (!$parent instanceof Category) {
            throw new NotFoundHttpException(sprintf('Категория %d не найдена', $query->pid));
        }

        switch ($query->filter) {
            case 'all':
                $where = "";
                break;

            case 'with-tpl':
                $where = "AND c2.tpl != 'none'";
                break;

            case 'without-tpl':
                $where = "AND c2.tpl = 'none'";
                break;

            case 'manual-tpl':
                $where = "AND c2.tpl = 'manual'";
                break;

            case 'auto-tpl':
                $where = "AND c2.tpl = 'auto";
                break;
        }

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\Template\Query\DTO\Category (
                    c.id,
                    c.pid,
                    c.name,
                    c.basename,
                    c.gender,
                    c.tpl,
                    c.isTplEnabled,
                    c.useExname,
                    c.aliasForId,
                    a.name,
                    CASE WHEN EXISTS (
                        SELECT 1 
                        FROM ContentBundle:Category cc 
                        WHERE cc.pid = c.id
                    ) 
                    THEN false ELSE true END
                )
            FROM ContentBundle:Category c 
            INNER JOIN ContentBundle:CategoryPath cp WITH cp.pid = c.id 
            INNER JOIN ContentBundle:Category c2 WITH c2.id = cp.id OR c2.aliasForId = cp.id
            LEFT OUTER JOIN ContentBundle:Category a WITH a.id = c.aliasForId
            WHERE c.pid = :pid {$where}
            GROUP BY c.id, a.id
            ORDER BY c.name
        ");
        $q->setParameter('pid', $parent->getId());

        return $q->getResult();
    }
}