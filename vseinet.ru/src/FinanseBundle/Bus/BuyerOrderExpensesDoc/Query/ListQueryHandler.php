<?php

namespace FinanseBundle\Bus\BuyerOrderExpensesDoc\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Doctrine\ORM\Query\ResultSetMapping;
use AppBundle\ORM\Query\DTORSM;
use DocumentBundle\SimpleTools\DocumentQueryHelper;
use FinanseBundle\Bus\BuyerOrderExpensesDoc\Query\DTO\DocumentList;

class ListQueryHandler extends MessageHandler
{

    public function handle(ListQuery $query)
    {


        $setParameters = [];
        $em = $this->getDoctrine()->getManager();

        // Запрос-счетчик

        $queryCount = "
            select
                count(did) as total
            from buyer_order_expenses_doc i
            ";

        // Запрос-список

        $queryList = "
            select" . DocumentQueryHelper::buildSelect('i') . "

            -- персональные поля документа

            from buyer_order_expenses_doc i" . DocumentQueryHelper::buildJoin('i') . "

            -- особые соединения
            ";

        // Просмотр архива

        if (!$query->withCompleted) {

            $queryList .= "
                where i.completed_at is null";

            $queryCount .= "
                where i.completed_at is null";
        } else {

            $queryList .= "
                where 1 = 1";

            $queryCount .= "
                where 1 = 1";
        }

        // Фильтр по статусу

        if (!empty($query->inStatuses)) {

            $queryList .= "
                and i.status_code in (:inStatuses)";

            $queryCount .= "
                and i.status_code in (:inStatuses)";

            $setParameters['inStatuses'] = $query->inStatuses;
        }

        // Фильтр по автору

        if (!empty($query->inCreatedBy)) {

            $queryList .= "
                and i.created_by in (:inCreatedBy)";

            $queryCount .= "
                and i.created_by in (:inCreatedBy)";

            $setParameters['inCreatedBy'] = $query->inCreatedBy;
        }

        //////////// Общее управление списком
        // Интервал дат

        if ($query->fromDate) {
            $queryList .= "
                and i.created_at >= :fromDate";
            $queryCount .= "
                and i.created_at >= :fromDate";
            $setParameters['fromDate'] = $query->fromDate . 'T00:00:00';
        }
        if ($query->toDate) {

            $queryList .= "
                and i.created_at <= :toDate";
            $queryCount .= "
                and i.created_at <= :toDate";
            $setParameters['toDate'] = $query->toDate . 'T23:59:59';
        }

        /////////////////////////////////////////////////////////////
        //
        //  Тут идет подсчет общего числа элементов списка,
        //  дальше обрабатывается только запрос списка
        //

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('total', 'total', 'integer');
        $totoal = $em->createNativeQuery($queryCount, $rsm)
                ->setParameters($setParameters)
                ->getSingleScalarResult();

        /////////////////////////////////////////////////////////////

        $queryList .= "
            order by i.created_at desc, i.did desc";

        // Пагинация

        if ($query->limit) {
            $queryList .= "
                limit {$query->limit}";
        }
        if ($query->page) {
            $offset = ($query->page - 1) * $query->limit;
            $queryList .= " offset {$offset}";
        }

        ////

        return new DTO\DocumentList(
                $em->createNativeQuery($queryList, new DTORSM(DTO\Documents::class, DTORSM::ARRAY_INDEX))
                        ->setParameters($setParameters)
                        ->getResult('DTOHydrator'), $totoal);
    }

}
