<?php

namespace DocumentBundle\Service;

use AppBundle\Container\ContainerAware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\EntityManager;
use AppBundle\ORM\Query\DTORSM;
use DocumentBundle\Prototipe\StatusesDTO;

class AnyDocStatus
{

    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Получить список статусов
     *
     * @param   string  $entityClassName              Имя класса документа
     * @param   bool    $onlyActive                   Показывать только активные статусы
     * @return  DocumentBundle\Prototipe\StatusesDTO  Список статусов документа
     */
    public function listAll(string $entityClassName, bool $onlyActive = null)
    {

        $statusTableName = $this->em
                        ->getClassMetadata($entityClassName)->getTableName() . '_status';
        //$this->statusHistoryTableName = statusTableName. '_history';

        $queryList = "
            select
                i.id as id,
                i.status_code,
                i.name,
                i.active,
                array_to_json(i.available_new_status_code) as available_new_status_code,
                i.completing

            from {$statusTableName} i
            ";

        if (!empty($onlyActive)) {

            $queryList .= "
            where i.active";
        }

        $queryList .= "
            order by i.id";

        $items = $this->em->createNativeQuery($queryList, new DTORSM(StatusesDTO::class, DTORSM::ARRAY_INDEX))
                ->getResult('DTOHydrator');

        return $items;
    }

    /**
     * Проверка допустимости измененения статуса документа
     *
     * @param string $entityClassName
     * @param string $documentStatusCode
     * @param string $newDocumentStatusCode
     * @throws BadRequestHttpException
     * @throws ConflictHttpException
     */
    public function checkNewStatus(string $entityClassName, string $oldDocumentStatusCode, string $newDocumentStatusCode)
    {

        $statusTableName = $this->em
                        ->getClassMetadata($entityClassName)->getTableName() . '_status';

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
                from {$statusTableName}
                where
                    status_code = :statusCode
                ";

        $newStatus = $this->em->createNativeQuery($statusQeryText, $rsm)
                ->setParameter('statusCode', $newDocumentStatusCode)
                ->getOneOrNullResult();

        if (empty($newStatus))
            throw new BadRequestHttpException('Новый статус не соответствует документу');

        $oldStatus = $this->em->createNativeQuery($statusQeryText, $rsm)
                ->setParameter('statusCode', $oldDocumentStatusCode)
                ->getOneOrNullResult();

        if (empty($oldStatus))
            throw new BadRequestHttpException('Старый статус не соответствует документу');

        // проверка возможности перехода на новый статус
        if (!preg_match("/\W" . $newStatus['statusCode'] . "\W/", $oldStatus['availableNewStatusCode'])) {
            throw new ConflictHttpException('Недопустимый новый статус документа');
        }

        return $newStatus;
    }

}
