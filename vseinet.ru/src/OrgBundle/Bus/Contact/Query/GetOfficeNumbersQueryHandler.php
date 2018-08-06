<?php

namespace OrgBundle\Bus\Contact\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetOfficeNumbersQueryHandler extends MessageHandler
{
    public function handle(GetOfficeNumbersQuery $query): array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $qParam = [];
        $qText = '
            SELECT
                NEW OrgBundle\Bus\Contact\Query\DTO\ContactInfo (
                    oc.contactId,
                    c.contactTypeCode,
                    c.value,
                    oc.departmentId,
                    od.number,
                    od.name,
                    oc.userId,
                    TRIM(CONCAT(
                        CASE WHEN pp.lastname IS NULL THEN \'\' ELSE CONCAT(pp.lastname, \' \') END,
                        CASE WHEN pp.firstname IS NULL THEN \'\' ELSE CONCAT(pp.firstname, \' \') END,
                        CASE WHEN pp.secondname IS NULL THEN \'\' ELSE pp.secondname END
                    ))
                )
            FROM OrgBundle:OrgContact AS oc
                INNER JOIN AppBundle:Contact AS c
                    WITH oc.contactId = c.id
                LEFT JOIN OrgBundle:Department AS od
                    WITH oc.departmentId = od.id
                LEFT JOIN AppBundle:User AS uu
                    WITH oc.userId = uu.id
                LEFT JOIN AppBundle:Person AS pp
                    WITH uu.personId = pp.id
        ';

        if ($query->contactType) {
            $qText .= '
                WHERE c.contactTypeCode = :contactType AND (';
            $qParam['contactType'] = $query->contactType;
        } else {
            $qText .= '
                WHERE (';
        }

        if ($query->withFree) {
            $qText .= '(oc.departmentId IS NULL AND oc.userId IS NULL) OR (';
        } else {
            $qText .= '(';
        }
        $qText .= '(oc.departmentId IS NOT NULL OR oc.userId IS NOT NULL)';

        if ($query->departmentId) {
            $qText .= '
                AND oc.departmentId = :departmentId';
            $qParam['departmentId'] = $query->departmentId;
        }
        if ($query->employeeId) {
            $qText .= '
                AND oc.userId = :employeeId';
            $qParam['employeeId'] = $query->employeeId;
        }

        $qText .= '))
            ORDER BY od.number, pp.lastname, pp.firstname, pp.secondname, c.contactTypeCode, c.value';

        /** @var DTO\ContactInfo[] $contacts */
        $contacts = $em->createQuery($qText)
            ->setParameters($qParam)
            ->getResult();

        return $contacts;
    }
}