<?php
namespace FinanseBundle\Bus\FinancialOperationDoc;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Doctrine\ORM\Query\ResultSetMapping;
use FinanseBundle\Entity\FinancialOperationDoc;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class FinancialOperationDocUpdate
{

    /**
     * Обновление документа
     *
     * @param \FinanseBundle\Entity\FinancialOperationDoc  $newDocument  регистрируемый документ
     * @param \FinanseBundle\Entity\FinancialOperationDoc  $oldDocument  старый регистрируемый документ
     * @param \Doctrine\ORM\EntityManager      $em           менеджер сущностей
     * @param \AppBundle\Entity\User           $currentUser  пользователь, регистрирующий документ
     */
    public static function Update(FinancialOperationDoc $newDocument, $relatedDocuments, FinancialOperationDoc $oldDocument, \Doctrine\ORM\EntityManager $em, \AppBundle\Entity\User $currentUser)
    {

        // Общие проверки документа на корректность заполнения

        if (!empty($oldDocument->getCompletedAt()))
            throw new ConflictHttpException('Изменение завершенного документа невозможно');

        if (!empty($oldDocument->getRegisteredAt()))
            throw new ConflictHttpException('Изменение зарегистрированного документа невозможно');
        
        if ($newDocument->getStatusCode() != $oldDocument->getStatusCode()) {

            // Проверка допустимости измененения статуса документа

            $rsm = new ResultSetMapping();
            $rsm->addScalarResult("name", "name", "string");
            $rsm->addScalarResult("status_code", "statusCode", "string");
            $rsm->addScalarResult("active", "active", "boolean");
            $rsm->addScalarResult("completing", "completing", "boolean");
            $rsm->addScalarResult("available_new_status_code", "availableNewStatusCode", "string");

            $statusQeryText = "
                select
                    name,
                    status_code,
                    active,
                    completing,
                    available_new_status_code
                from financial_operation_doc_status
                where
                    status_code = :statusCode
                ";

            $newStatus = $em->createNativeQuery($statusQeryText, $rsm)
                    ->setParameter('statusCode', $newDocument->getStatusCode())
                    ->getOneOrNullResult();

            if (empty($newStatus))
                throw new BadRequestHttpException('Новый статус не соответствует документу');

            $oldStatus = $em->createNativeQuery($statusQeryText, $rsm)
                    ->setParameter('statusCode', $oldDocument->getStatusCode())
                    ->getOneOrNullResult();

            if (empty($oldStatus))
                throw new BadRequestHttpException('Старый статус не соответствует документу');

            // проверка возможности перехода на новый статус
            if (!preg_match("/\W" . $newStatus['statusCode'] . "\W/", $oldStatus['availableNewStatusCode'])) {
                throw new ConflictHttpException('Недопустимый новый статус документа');
            }

            if ($newStatus['completing']) {

                $newDocument->setCompletedAt(new \DateTime);
                $newDocument->setCompletedBy($currentUser->getId());
            } else {

                $newDocument->setCompletedAt();
                $newDocument->setCompletedBy();
            }
        }

        // Особые проверки документа на корректность заполнения

        // Удаление старого списка связанных документов

        $documentNumber = $em->createNativeQuery(
            "delete from financial_operation_doc_related_document where financial_operation_doc_did = {$command->id};",
            new ResultSetMapping())->execute();

     
        // Проверка и запись нового списка связанных документов

        $amount = abs($document->getAmount());

        if (!empty($command->relatedDocuments)) {

            foreach ($command->relatedDocuments as $relatedDocument) {

                $amount -= abs($relatedDocument->amount);

                $relatedDocument = new FinancialOperationDocRelatedDocument();

                $relatedDocument->setBankOperationDocumentId($document->getDId());
                $relatedDocument->setRelatedDocumentId($relatedDocument->documentId);
                $relatedDocument->setAmount($relatedDocument->amount);
                $em->persist($relatedDocument);

            }

            if (0 != $amount)
                throw new BadRequestHttpException('Сумма операции не полностью разнесена по связанным документам');

            $em->flush();

        }

        //////////////////////////////////////////////////////

        $em->persist($newDocument);
        $em->flush();
        
    }

}
