<?php

namespace ContentBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use ContentBundle\Bus\GeoRoom\Query;
//use ContentBundle\Bus\GeoRoom\Command;

/**
 * @VIA\Section("Geo-графия")
 */
class GeoRoomController extends RestController
{

    /**
     * @VIA\Get(
     *     path="/geoRooms/",
     *     description="Получить список комнат/офисов",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ContentBundle\Bus\GeoRoom\Query\ListQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ContentBundle\Entity\GeoRoom")
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
     *     path="/geoRooms/foundResults/",
     *     description="Получить список комнат/офисов с фильтрами",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ContentBundle\Bus\GeoRoom\Query\FoundResultsQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ContentBundle\Bus\GeoRoom\Query\DTO\FoundResults")
     *     }
     * )
     */
    public function searchAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\FoundResultsQuery($request->query->all()), $items);

        return $items;
    }

    /**
     * @VIA\Get(
     *     path="/geoRooms/{id}/",
     *     description="Получить комнату/офис",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ContentBundle\Bus\GeoRoom\Query\ItemQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="ContentBundle\Entity\GeoRoom")
     *     }
     * )
     */
    public function getItemAction(int $id, Request $request)
    {
        $this->get('query_bus')->handle(new Query\ItemQuery($request->query->all(), ['id' => $id]), $item);

        return $item;
    }

}
