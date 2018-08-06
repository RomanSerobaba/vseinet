<?php

namespace OrgBundle\Bus\Employee\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetDocumentsQueryHandler extends MessageHandler
{
    public function handle(GetDocumentsQuery $query)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        // Select Documents
        /** @var DTO\Document[] $documents */
        $documents = $em->createQuery('
                SELECT
                    NEW OrgBundle\Bus\Employee\Query\DTO\Document (
                        ed.id,
                        ed.name,
                        ed.isNecessary,
                        ed.isCommentAllowed,
                        ed.hasDueDate,
                        etd.id,
                        etd.comment,
                        CASE
                            WHEN etd.checkedAt IS NOT NULL
                            THEN TRUE
                            ELSE FALSE
                        END,
                        etd.dueDate
                    )
                FROM OrgBundle:EmployeeDocument AS ed
                    LEFT JOIN OrgBundle:EmployeeToDocument AS etd
                        WITH ed.id = etd.documentId AND etd.userId = :userId
                WHERE ed.isActive = TRUE
                ORDER BY ed.name
            ')
            ->setParameter('userId', $query->id)
            ->getResult();

        return $documents;
    }
}