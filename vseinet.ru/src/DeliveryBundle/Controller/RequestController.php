<?php

namespace DeliveryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use DeliveryBundle\Bus\Request\Query;
use DeliveryBundle\Bus\Request\Command;

/**
 * @VIA\Section("Заявка на доставку")
 */
class RequestController extends Controller
{
    /**
     * @VIA\Get(
     *     path="/deliveryRequests/",
     *     description="Получить список заявок на доставку",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="DeliveryBundle\Bus\Request\Query\GetListQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="DeliveryBundle\Bus\Request\Query\DTO\Request", type="array")
     *     }
     * )
     */
    public function getListAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetListQuery($request->query->all()), $requests);

        return $requests;
    }
}
