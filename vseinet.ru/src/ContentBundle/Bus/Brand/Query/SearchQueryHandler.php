<?php 

namespace ContentBundle\Bus\Brand\Query;

use AppBundle\Bus\Message\MessageHandler;

class SearchQueryHandler extends MessageHandler
{
    public function handle(SearchQuery $query)
    {
        $name = mb_strtolower($query->name, 'UTF-8').'%';

        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT 
                NEW ContentBundle\Bus\Brand\Query\DTO\FoundResult (
                    b.id,
                    b.name,
                    b.url
                )
            FROM ContentBundle:Brand b 
            LEFT OUTER JOIN ContentBundle:BrandPseudo bp WITH bp.brandId = b.id 
            WHERE LOWER(b.name) LIKE :name OR LOWER(bp.name) LIKE :name_pseudo
            ORDER BY b.name
        ");
        $q->setParameter('name', $name);
        $q->setParameter('name_pseudo', $name);
        $q->setMaxResults($query->limit);

        return $q->getArrayResult();
    }
}