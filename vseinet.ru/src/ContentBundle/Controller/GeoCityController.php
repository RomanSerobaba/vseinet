<?php

namespace ContentBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use ContentBundle\Bus\GeoCity\Query;
//use ContentBundle\Bus\GeoCity\Command;

/**
 * @VIA\Section("Geo-графия")
 */
class GeoCityController extends RestController
{

    /**
     * @VIA\Get(
     *     path="/geoCities/",
     *     description="Получить список строений/сооружений",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ContentBundle\Bus\GeoCity\Query\ListQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ContentBundle\Entity\GeoCity")
     *     }
     * )
     */
    public function GetAction(Request $request)
    {
        
        $this->get('query_bus')->handle(new Query\ListQuery($request->query->all()), $items);

        return $items;
    }

    /**
     * @VIA\Get(
     *     path="/geoCities/{id}/",
     *     description="Получить строение/сооружение",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ContentBundle\Bus\GeoCity\Query\ItemQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="ContentBundle\Entity\GeoCity")
     *     }
     * )
     */
    public function ResetAction(int $id, Request $request)
    {
        $this->get('query_bus')->handle(new Query\ItemQuery($request->query->all(), ['id' => $id]), $item);

        return $item;
    }

}
