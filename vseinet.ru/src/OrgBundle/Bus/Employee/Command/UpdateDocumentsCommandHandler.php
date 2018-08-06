<?php

namespace OrgBundle\Bus\Employee\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityNotFoundException;
use OrgBundle\Entity\Employee;
use OrgBundle\Entity\EmployeeDocument;
use OrgBundle\Entity\EmployeeToDocument;

class UpdateDocumentsCommandHandler extends MessageHandler
{
    public function handle(UpdateDocumentsCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();


        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();


        /** @var Employee $employee */
        $employee = $em->getRepository(Employee::class)->find($command->id);

        if (!$employee)
            throw new EntityNotFoundException('Сотрудник не найден');


        // Select Documents
        /** @var EmployeeDocument[] $documents */
        $documents = $em->createQuery('
                SELECT ed
                FROM OrgBundle:EmployeeDocument AS ed
                WHERE ed.isActive = TRUE
                ORDER BY ed.id
            ')
            ->getResult();

        $documentsIds = array_flip(array_map(function($doc){ return $doc->getId(); }, $documents));


        // Select UserDocuments
        /** @var EmployeeToDocument[] $userDocuments */
        $userDocuments = $em->createQuery('
                SELECT etd
                FROM OrgBundle:EmployeeDocument AS ed
                    INNER JOIN OrgBundle:EmployeeToDocument AS etd
                        WITH ed.id = etd.documentId AND etd.userId = :userId
                WHERE ed.isActive = TRUE
                ORDER BY etd.id
            ')
            ->setParameter('userId', $command->id)
            ->getResult();

        $userDocumentsIds = array_flip(array_map(function($doc){ return $doc->getDocumentId(); }, $userDocuments));


        foreach ($command->documents as $documentInfo) {
            if (!isset($documentsIds[$documentInfo->documerntId]))
                continue;

            $document = $documents[$documentsIds[$documentInfo->documerntId]];

            if (isset($userDocumentsIds[$document->getId()])) {
                $userDocument = $userDocuments[$userDocumentsIds[$document->getId()]];
                $userDocument->setComment($documentInfo->comment);
                $userDocument->setDueDate($documentInfo->dueDate);

                if (!$userDocument->getCheckedAt() && $documentInfo->isChecked) {
                    $userDocument->setCheckedAt(new \DateTime());
                    $userDocument->setCheckedBy($currentUser->getId());
                } elseif ($userDocument->getCheckedAt() && !$documentInfo->isChecked) {
                    $userDocument->setCheckedAt(null);
                }
                $em->persist($userDocument);

            } elseif ($documentInfo->comment || $documentInfo->isChecked || $documentInfo->dueDate) {
                $userDocument = new EmployeeToDocument();
                $userDocument->setUserId($employee->getUserId());
                $userDocument->setDocumentId($document->getId());
                $userDocument->setCreatedAt(new \DateTime());
                $userDocument->setCreatedBy($currentUser->getId());
                $userDocument->setComment($documentInfo->comment);
                $userDocument->setDueDate($documentInfo->dueDate);

                if ($documentInfo->isChecked) {
                    $userDocument->setCheckedAt(new \DateTime());
                    $userDocument->setCheckedBy($currentUser->getId());
                }
                $em->persist($userDocument);
            }
        }
        $em->flush();
    }
}