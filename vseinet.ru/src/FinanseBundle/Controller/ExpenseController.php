<?php

namespace FinanseBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use FinanseBundle\Bus\ExpenseOperations\Query;

/**
 * @VIA\Section("Расходы")
 */
class ExpenseController extends RestController
{

    /**
     * @VIA\Get(
     *     path="/expenses/",
     *     title="Получить сводный список расходов",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="FinanseBundle\Bus\ExpenseOperations\Query\ListExpensesQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="FinanseBundle\Bus\ExpenseOperations\Query\DTO\ListExpensesDTO")
     *     }
     * )
     */
    public function listAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\ListExpensesQuery($request->query->all()), $entity);

        return $entity;
    }

}
