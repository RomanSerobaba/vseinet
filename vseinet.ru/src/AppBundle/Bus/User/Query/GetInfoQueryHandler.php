<?php 

namespace AppBundle\Bus\User\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetInfoQueryHandler extends MessageHandler
{
    public function handle(GetInfoQuery $query)
    {
        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT 
                NEW AppBundle\Bus\User\Query\DTO\UserInfo (
                    u.id,
                    p.lastname,
                    p.firstname,
                    p.secondname,
                    p.gender,
                    p.birthday,
                    gc.id,
                    gc.name,
                    u.isMarketingSubscribed
                )
            FROM AppBundle:User AS u
            INNER JOIN AppBundle:Person AS p WITH u.personId = p.id
            LEFT OUTER JOIN AppBundle:GeoCity AS gc WITH gc.id = u.geoCityId
            WHERE u.id = :id
        ");
        $q->setParameter('id', $this->getUser()->getId());
        $info = $q->getSingleResult();

        return $info;
    }
}
