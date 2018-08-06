<?php

namespace OrgBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use OrgBundle\Bus\Counteragents\Query;

/**
 * @VIA\Section("Контрагенты")
 */
class CounteragentsController extends RestController
{

    /**
     * @VIA\Get(
     *     path="/ourCounteragents/forOrdersShipping/",
     *     description="Список наших контрагентов",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Counteragents\Query\GetOurCounteragentsQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="OrgBundle\Bus\Counteragents\Query\DTO\OurCounteragents")
     *     }
     * )
     */
    public function getTreeAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetOurCounteragentsQuery($request->query->all()), $items);

        return $items;
    }

    /**
     * @VIA\Get(
     *     path="/financialResources/",
     *     description="Получение финансовых источников по типу",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="OrgBundle\Bus\Counteragents\Query\GetFinancialResourcesQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="OrgBundle\Bus\Counteragents\Query\DTO\FinancialResource")
     *     }
     * )
     */
    public function GetFinancialResourcesAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetFinancialResourcesQuery($request->query->all()), $finResources);

        return $finResources;
    }
}
