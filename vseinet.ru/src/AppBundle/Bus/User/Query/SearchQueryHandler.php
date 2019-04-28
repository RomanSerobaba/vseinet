<?php

namespace AppBundle\Bus\User\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\ContactTypeCode;

class SearchQueryHandler extends MessageHandler
{
    public function handle(SearchQuery $query)
    {
        if ('phone' == $query->field) {
            $where = 'mobile.value LIKE :phone';
            $parameters = ['phone' => $query->q.'%'];
        } else {
            $where = "(LOWER(p.lastname) LIKE LOWER(:lastname) OR LOWER(CONCAT_WS(' ', p.lastname, p.firstname, p.secondname)) LIKE LOWER(:fullname))";
            $parameters = ['lastname' => $query->q.'%', 'fullname' => '%'.$query->q.'%'];
        }

        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT
                NEW AppBundle\Bus\User\Query\DTO\FoundUser (
                    u.id,
                    CONCAT_WS(' ', p.lastname, p.firstname, p.secondname),
                    mobile.value,
                    FIRST(
                        SELECT email.value
                        FROM AppBundle:Contact AS email
                        WHERE p.id = email.personId AND email.contactTypeCode = :contactTypeCode_EMAIL AND email.isMain = TRUE
                    ),
                    FIRST(
                        SELECT phone.value
                        FROM AppBundle:Contact AS phone
                        WHERE p.id = phone.personId AND phone.contactTypeCode IN (:contactTypeCode_MOBILE, :contactTypeCode_PHONE) AND phone.isMain = TRUE
                    ),
                    'user',
                    eh.orgEmployeeUserId
                )
            FROM AppBundle:User AS u
            JOIN AppBundle:Person AS p WITH p.id = u.personId
            LEFT JOIN AppBundle:EmploymentHistory AS eh WITH eh.orgEmployeeUserId = u.id AND eh.hiredAt IS NOT NULL AND eh.firedAt IS NULL
            LEFT JOIN AppBundle:Contact AS mobile WITH p.id = mobile.personId AND mobile.contactTypeCode = :contactTypeCode_MOBILE AND mobile.isMain = TRUE
            WHERE {$where}
        ");
        $q->setParameters($parameters + [
                'contactTypeCode_MOBILE' => ContactTypeCode::MOBILE,
                'contactTypeCode_EMAIL' => ContactTypeCode::EMAIL,
                'contactTypeCode_PHONE' => ContactTypeCode::PHONE,
            ]);
        $q->setMaxResults($query->limit);
        $users = $q->getResult();

        if (count($users) < $query->limit) {
            if ('phone' == $query->field) {
                $where = 'cu.phone LIKE :phone';
                $parameters = ['phone' => $query->q.'%'];
            } else {
                $where = 'LOWER(cu.fullname) LIKE LOWER(:fullname)';
                $parameters = ['fullname' => '%'.$query->q.'%'];
            }

            $q = $this->getDoctrine()->getManager()->createQuery("
                SELECT
                    NEW AppBundle\Bus\User\Query\DTO\FoundUser (
                        cu.id,
                        cu.fullname,
                        cu.phone,
                        cu.email,
                        cu.additionalPhone,
                        'comuser',
                        FALSE
                    )
                FROM AppBundle:Comuser AS cu
                WHERE {$where}
            ");
            $q->setParameters($parameters);
            $q->setMaxResults($query->limit - count($users));
            $users = array_merge($users, $q->getResult());
        }

        return $users;
    }
}
