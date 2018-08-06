<?php

namespace FinanseBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use FinanseBundle\Bus\FinancialCounteragent\Query;

/**
 * @VIA\Section("Финансы - Контрагенты (Пользователи, орнганизации)")
 */
class FinancialCounteragentController extends RestController
{

    /**
     * @VIA\Get(
     *     path="/financialCounteragents/foundResults/",
     *     description="Получить список финансовых контрагентов",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="FinanseBundle\Bus\FinancialCounteragent\Query\FoundResultsQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="FinanseBundle\Bus\FinancialCounteragent\Query\DTO\FinancialCounteragentDTO")
     *     }
     * )
     */
    public function listAction(Request $request)
    {

        $this->get('query_bus')->handle(new Query\FoundResultsQuery($request->query->all()), $items);

        return $items;

    }

}
