<?php

namespace FinanseBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use FinanseBundle\Bus\FinancialOperations\Query;

/**
 * @VIA\Section("Финансы")
 */
class FinanceController extends RestController
{
    /**
     * @VIA\Get(
     *     path="/financialOperations/",
     *     title="Получить список финансовых операций (документов)",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="FinanseBundle\Bus\FinancialOperations\Query\ListQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="FinanseBundle\Bus\FinancialOperations\Query\DTO\DocumentList")
     *     }
     * )
     */
    public function listAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\ListQuery($request->query->all()), $entity);

        return $entity;
    }
    
}
