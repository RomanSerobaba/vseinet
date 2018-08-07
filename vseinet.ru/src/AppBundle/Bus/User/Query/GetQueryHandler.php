<?php 

namespace AppBundle\Bus\User\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetQueryHandler extends MessageHandler
{
    public function handle(GetQuery $query)
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
            LEFT OUTER JOIN GeoBundle:GeoCity AS gc WITH gc.id = u.cityId
            WHERE u.id = :id
        ");
        $q->setParameter('id', $this->get('user.identity')->getUser()->getId());
        $info = $q->getSingleResult();

        return $info;
    }
}
