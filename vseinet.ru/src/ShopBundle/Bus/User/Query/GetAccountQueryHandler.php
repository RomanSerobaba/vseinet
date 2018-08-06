<?php 

namespace ShopBundle\Bus\User\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use AppBundle\ORM\Query\DTORSM;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;


class GetAccountQueryHandler extends MessageHandler
{
    public function handle(GetAccountQuery $query)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();
        $id = $currentUser->getId();

        if (!$currentUser) {
            throw new AuthenticationCredentialsNotFoundException('Необходима авторизация');
        }

        $q = $em->createNativeQuery('
            SELECT
                vup."id", 
                vup."user_id", 
                vup."firstname", 
                vup."secondname", 
                vup."lastname", 
                vup."birthday", 
                vup."fullname", 
                vup."phone", 
                vup."mobile", 
                vup."email",
                vup."gender",
                u.is_marketing_subscribed,
                u.is_transactional_subscribed,
                gc.id as geo_city_id,
                gc.NAME as geo_city_name,
                NULL AS street, 
                NULL AS home, 
                NULL AS housing, 
                NULL AS apartment, 
                NULL AS floor, 
                NULL AS has_lift
            FROM
                func_view_user_person ( :id ) AS vup
                INNER JOIN "user" u ON u.id = vup.id
                LEFT JOIN geo_city gc ON gc."id" = u.geo_city_id
        ', new DTORSM(\ShopBundle\Bus\User\Query\DTO\Account::class));
        $q->setParameter('id', $id);

        return $q->getResult('DTOHydrator');
    }
}