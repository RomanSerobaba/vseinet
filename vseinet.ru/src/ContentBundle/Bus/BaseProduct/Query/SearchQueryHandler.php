<?php 

namespace ContentBundle\Bus\BaseProduct\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\SphinxQL\SphinxQL;
use ContentBundle\Entity\BaseProduct;

class SearchQueryHandler extends MessageHandler
{
    public function handle(SearchQuery $query)
    {
        if (preg_match('/^\d+$/', $query->q)) {
            $q = $this->getDoctrine()->getManager()->createQuery("
                SELECT 
                    NEW ContentBundle\Bus\BaseProduct\Query\DTO\FoundResult (
                        bp.id,
                        bp.name
                    )
                FROM ContentBundle:BaseProduct bp 
                WHERE bp.id = :queryString
            ");
            $q->setParameter('queryString', $query->q);
            $products = $q->getResult();
        }
        else {            
            $products = [];
        }

        $sphinxql = $this->get('sphinxql');
        $str = $sphinxql->escapeMatch($query->q);
        $snippet = $sphinxql->escape($query->q);

        $results = $sphinxql->execute("
            SELECT id, WEIGHT() AS weight, SNIPPET(name, '{$snippet}') AS label
            FROM base_product
            WHERE MATCH('@name {$str}') AND killbill = 0 
            ORDER BY weight DESC
            LIMIT {$query->limit}
            OPTION ranker=expr('sum((4*lcs+2*(min_hit_pos==1)+exact_hit)*user_weight)*1000+bm25')
        ");

        if (!empty($results[0])) {
            foreach ($results[0] as $item) {
                $products[] = new DTO\FoundResult($item['id'], $item['label']);
            }
        }

        return $products;
    }
}