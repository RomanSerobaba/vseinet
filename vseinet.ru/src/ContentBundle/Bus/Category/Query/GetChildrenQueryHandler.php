<?php 

namespace ContentBundle\Bus\Category\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Category;

/**
 * @deprecated
 */
class GetChildrenQueryHandler extends MessageHandler 
{
    public function handle(GetChildrenQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository(Category::class)->find($query->id);
        if (!$category instanceof Category) {
            throw new NotFoundHttpException();
        }

        $category->children = $this->getChildren($query->id);

        return $category;
    }

    protected function getChildren($id)
    {
        $query = $this->getDoctrine()->getManager()->createQuery("
            SELECT 
                c.id, c.name, c.pid, c.aliasForId,
                CASE WHEN EXISTS (
                    SELECT 1 
                    FROM ContentBundle:Category cc 
                    WHERE cc.pid = c.id
                ) 
                THEN false ELSE true END isLeaf
            FROM ContentBundle:Category c 
            WHERE c.pid = :id 
            ORDER BY c.name 
        ");
        $query->setParameter('id', $id);

        return $query->execute();
    }
}