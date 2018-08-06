<?php 

namespace ContentBundle\Bus\Category\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Bus\Category\Query\DTO\SearchResult;

class SearchQueryHandler extends MessageHandler
{
    public function handle(SearchQuery $query)
    {   
        $sphinxql = $this->get('sphinxql');
        $str = $sphinxql->escapeMatch($query->q);

        $sql = "
            SELECT id, name, WEIGHT() as weight
            FROM category 
            WHERE MATCH('{$str}') AND killbill = 0 AND is_leaf = 1
            ORDER BY weight DESC 
            LIMIT {$query->limit}
            OPTION ranker=expr('sum((4*lcs+2*(min_hit_pos==1)+exact_hit)*user_weight)*1000+bm25')
        ";
        $results = $sphinxql->execute($sql);
        $categories = $sphinxql->fetchAssoc($results[0]);

        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\Category\Query\DTO\CategoryFound (
                    c.id,
                    c.name,
                    c.pid 
                )
            FROM ContentBundle:Category c 
            INNER JOIN ContentBundle:CategoryPath cp WITH cp.pid = c.id
            WHERE cp.id IN (:categoryIds)
            GROUP BY c.id, cp.plevel 
            ORDER BY cp.plevel
        ");
        $q->setParameter('categoryIds', array_keys($categories));
        $cwb = $q->getResult('IndexByHydrator');      
        foreach ($categories as $id => $category) {
            $category = $cwb[$id];
            $category->breadcrumbs = [];
            $pid = $category->pid;
            while ($pid) {
                array_unshift($category->breadcrumbs, $cwb[$pid]->name);
                $pid = $cwb[$pid]->pid;
            }

            $name = preg_replace('/'.preg_quote($query->q, '/').'/ui', '<b>$0</b>', $category->name);
            $categories[$id] = new SearchResult($id, implode(' / ', $category->breadcrumbs) .' / '.$name);
        }

        return array_values($categories);
    }
}