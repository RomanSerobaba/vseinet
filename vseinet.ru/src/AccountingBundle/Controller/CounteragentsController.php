<?php

namespace AccountingBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use AccountingBundle\Bus\Counteragents\Query;

/**
 * @VIA\Section("Контрагенты")
 */
class CounteragentsController extends RestController
{
    /**
     * @VIA\Get(
     *     path="/counteragents/",
     *     description="Получение контрагентов",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="AccountingBundle\Bus\Counteragents\Query\GetCounteragentsQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array")
     *     }
     * )
     */
    public function indexAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetCounteragentsQuery($request->query->all()), $clients);

        return $clients;
    }
}
