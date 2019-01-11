<?php

namespace AppBundle\Bus\User\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\ContactTypeCode;

class GetUserDataQueryHandler extends MessageHandler
{
    public function handle(GetUserDataQuery $query)
    {
        $data = new DTO\UserData();

        if (null !== $user = $this->getUser()) {
            $data->userId = $user->getId();
            $data->fullname = $user->person->getFullname();

            $em = $this->getDoctrine()->getManager();

            $q = $em->createQuery("
                SELECT
                    c,
                    CASE WHEN c.isMain = true THEN 1 ELSE 2 END AS HIDDEN ORD1,
                    CASE WHEN c.contactTypeCode = :mobileOrd THEN 1 ELSE 2 END AS HIDDEN ORD2
                FROM AppBundle:Contact AS c
                WHERE c.personId = :personId AND c.contactTypeCode IN (:mobile, :phone)
                ORDER BY ORD1 ASC, ORD2 ASC
            ");
            $q->setParameter('personId', $user->getPersonId());
            $q->setParameter('mobile', ContactTypeCode::MOBILE);
            $q->setParameter('mobileOrd', ContactTypeCode::MOBILE);
            $q->setParameter('phone', ContactTypeCode::PHONE);
            $data->phoneList = $q->getResult();
            if (!empty($data->phoneList)) {
                $data->phone = $data->phoneList[0]->getValue();
            }

            $q = $em->createQuery("
                SELECT
                    c,
                    CASE WHEN c.isMain = true THEN 1 ELSE 2 END AS HIDDEN ORD
                FROM AppBundle:Contact AS c
                WHERE c.personId = :personId AND c.contactTypeCode IN (:email)
                ORDER BY ORD ASC
            ");
            $q->setParameter('personId', $user->getPersonId());
            $q->setParameter('email', ContactTypeCode::EMAIL);
            $data->emailList = $q->getResult();
            if (!empty($data->emailList)) {
                $data->email = $data->emailList[0]->getValue();
            }
        }

        return $data;
    }
}
