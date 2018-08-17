<?php 

namespace AppBundle\Bus\User\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetContactsQueryHandler extends MessageHandler
{
    public function handle(GetContactsQuery $query)
    {
        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT 
                NEW AppBundle\Bus\User\Query\DTO\Contact (
                    c.id,
                    c.contactTypeCode,
                    c.value,
                    c.comment,
                    c.isMain
                )
            FROM AppBundle:Contact AS c 
            WHERE c.personId = :personId
            ORDER BY c.contactTypeCode ASC, c.isMain DESC
        ");
        $q->setParameter('personId', $this->getUser()->getPersonId());
        $contacts = $q->getArrayResult();

        return $contacts;
    }
}
