<?php

namespace AccountingBundle\Bus\Clients\Query;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\Query\ResultSetMapping;

class GetClientQueryHandler extends MessageHandler
{
    /**
     * @param GetClientQuery $query
     *
     * @return array
     */
    public function handle(GetClientQuery $query): array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $q = $em->createNativeQuery("
            SELECT
                u.id,
                u.is_marketing_subscribed AS isMarketingSubscribed,
                u.is_transactional_subscribed AS isTransactionalSubscribed,
                p.id AS personId,
                p.firstname,
                p.secondname,
                p.lastname,
                p.birthday,
                p.gender,
                c.id cityId,
                c.name city,
                ar.code roleCode,
                ar.summary roleName,
                ar.sort_order rolePosition,
                string_agg(CONCAT(cnt.id, '|', cnt.contact_type_code, '|', cnt.value), ',') as contacts
            FROM
                \"user\" u
                INNER JOIN person AS p ON p.id = u.person_id
                LEFT JOIN contact cnt ON cnt.person_id = p.id
                INNER JOIN user_to_acl_subrole AS utasr ON utasr.user_id = u.id
                INNER JOIN acl_subrole AS asr ON asr.id = utasr.acl_subrole_id
                INNER JOIN acl_role AS ar ON ar.id = asr.acl_role_id	
                LEFT OUTER JOIN geo_city c ON c.id = u.geo_city_id
                LEFT OUTER JOIN org_employee ed ON ed.user_id = u.id
            WHERE
                u.id = :id
            GROUP BY
                u.id, p.id, c.id, ar.id",
            new ResultSetMapping()
        );
        $q->setParameter('id', $query->id);

        $user = $q->getResult('ListHydrator');

        $list = [];
        if (!empty($user['contacts'])) {
            $parts = explode(',', $user['contacts']);
            foreach ($parts as $part) {
                $contacts = explode('|', $part);

                $list[$contacts[1]][$contacts[0]] = $contacts[2];
            }
        }

        $user['contacts'] = $list;

        $q = $em->createNativeQuery("
            SELECT 
                id,
                name
            FROM 
                geo_city
            ORDER BY 
                name",
            new ResultSetMapping()
        );

        return ['cities' => $q->getResult('ListHydrator'), 'user' => $user,];
    }
}