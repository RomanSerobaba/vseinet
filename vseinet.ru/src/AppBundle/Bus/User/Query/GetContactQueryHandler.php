<?php 

namespace AppBundle\Bus\User\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetContactQueryHandler extends MessageHandler
{
    public function handle(GetContactQuery $query)
    {
        $q = $this->getDoctrine()->getManager();->createQuery("
            SELECT 
                NEW AppBundle\Bus\User\Query\DTO\Contact (
                    c.id,
                    c.contactTypeCode,
                    c.value,
                    c.comment,
                    c.isMain
                )
            FROM AppBundle:Contact AS c 
            WHERE c.id = :id
            ORDER BY c.contactTypeCode ASC, c.isMain DESC
        ");
        $q->setParameter('id', $query->id);
        $contact = $q->getSingleResult();

        return $contact;
    }
}
