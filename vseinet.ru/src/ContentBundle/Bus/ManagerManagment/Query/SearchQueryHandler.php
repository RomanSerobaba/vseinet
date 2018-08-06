<?php 

namespace ContentBundle\Bus\ManagerManagment\Query;

use AppBundle\Bus\Message\MessageHandler;

class SearchQueryHandler extends MessageHandler
{
    public function handle(SearchQuery $query)
    {
        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT 
                NEW ContentBundle\Bus\ManagerManagment\Query\DTO\Employee (
                    e.userId, 
                    TRIM(CONCAT(COALESCE(p.lastname, ''), ' ', COALESCE(p.firstname, ''), ' ', COALESCE(p.secondname, ''))) 
                )
            FROM OrgBundle:Employee e 
            INNER JOIN AppBundle:User u WITH u.id = e.userId 
            INNER JOIN AppBundle:Person p WITH p.id = u.personId 
            WHERE (LOWER(p.lastname) LIKE :q OR LOWER(p.firstname) LIKE :q OR LOWER(p.secondname) LIKE :q)
                AND NOT EXISTS (
                    SELECT 1 
                    FROM ContentBundle:Manager m 
                    WHERE m.userId = e.userId AND m.isActive = true
                ) 
            ORDER BY p.lastname, p.firstname, p.secondname 
        ");
        $q->setParameter('q', mb_strtolower($query->q, 'UTF-8').'%');
        $q->setMaxResults($query->limit);

        return $q->getResult();
    }
}