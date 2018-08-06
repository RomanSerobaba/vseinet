<?php

namespace FinanseBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use FinanseBundle\Bus\ItemOfExpenses\Query;

/**
 * @VIA\Section("Расходы - статьи расходов")
 */
class ItemOfExpensesController extends RestController
{
    /**
     * @VIA\Get(
     *     path="/itemOfExpense/foundResults/",
     *     title="Получить список статей расхода",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="FinanseBundle\Bus\ItemOfExpenses\Query\FoundResultsQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="FinanseBundle\Bus\ItemOfExpenses\Query\DTO\ListDTO")
     *     }
     * )
     */
    public function listAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\FoundResultsQuery($request->query->all()), $entity);

        return $entity;
    }
    
}
