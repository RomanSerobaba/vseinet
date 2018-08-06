<?php

namespace FinanseBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use FinanseBundle\Bus\Equipment\Query;

/**
 * @VIA\Section("Финансы")
 */
class EquipmentController extends RestController
{
    /**
     * @VIA\Get(
     *     path="/equipment/foundResults/",
     *     title="Получить список оборудования",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="FinanseBundle\Bus\Equipment\Query\FoundResultsQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="FinanseBundle\Bus\Equipment\Query\DTO\EquipmentDTO")
     *     }
     * )
     */
    public function listAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\FoundResultsQuery($request->query->all()), $entity);

        return $entity;
    }
    
}
