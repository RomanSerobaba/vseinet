<?php

namespace DeliveryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use DeliveryBundle\Bus\TransportCompany\Query;
use DeliveryBundle\Bus\TransportCompany\Command;

/**
 * @VIA\Section("Транспортная компания")
 */
class TransportCompanyController extends Controller
{
    /**
     * @VIA\Get(
     *     path="/transportCompanies/foundResults/",
     *     description="Получить список транспортных компаний для фильтра",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="DeliveryBundle\Bus\TransportCompany\Query\GetListForFilterQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="DeliveryBundle\Bus\TransportCompany\Query\DTO\TransportCompanyForFilter", type="array")
     *     }
     * )
     */
    public function getListForFilterAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetListForFilterQuery($request->query->all()), $deliveries);

        return $deliveries;
    }    
}
