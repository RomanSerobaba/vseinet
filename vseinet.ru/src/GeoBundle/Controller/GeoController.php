<?php

namespace GeoBundle\Controller;

use AppBundle\Annotation as VIA;
use Doctrine\ORM\EntityNotFoundException;
use GeoBundle\Bus\Geo\Query;
use GeoBundle\Service\DTO\City;
use GeoBundle\Service\DTO\CityInfo;
use GeoBundle\Service\DTO\Region;
use GeoBundle\Service\DTO\RegionInfo;
use GeoBundle\Service\DTO\Street;
use GeoBundle\Service\DTO\StreetInfo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @VIA\Section("Гео данные")
 */
class GeoController extends Controller
{
    /**
     * @VIA\Get(
     *     path="/geo/regions/foundResults/",
     *     description="Получить список регионов (для автокомплита)",
     *     parameters={
     *          @VIA\Parameter(model="GeoBundle\Bus\Geo\Query\SearchRegionsQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="GeoBundle\Service\DTO\Region")
     *     }
     * )
     */
    public function SearchRegionsAction(Request $request)
    {
        $query = new Query\SearchRegionsQuery($request->query->all());

        /** @var Region[] $regions */
        $regions = $this->get('city.identity')->searchRegions($query->q);

        return new JsonResponse($regions);
    }

    /**
     * @VIA\Get(
     *     path="/geo/regions/{id}/info/",
     *     description="Получить информацию о регионе по id",
     *     parameters={
     *          @VIA\Parameter(model="GeoBundle\Bus\Geo\Query\GetRegionInfoQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="GeoBundle\Service\DTO\RegionInfo")
     *     }
     * )
     */
    public function GetRegionInfoAction(int $id, Request $request)
    {
        $query = new Query\GetRegionInfoQuery($request->query->all(), ['id' => $id]);

        /** @var RegionInfo $region */
        $region = $this->get('city.identity')->getRegionInfo($query->id);

        if (!$region)
            throw new EntityNotFoundException('Нет такого региона');

        return new JsonResponse($region);
    }

    /**
     * @VIA\Get(
     *     path="/geo/cities/foundResults/",
     *     description="Получить список городов для автокомплита",
     *     parameters={
     *          @VIA\Parameter(model="GeoBundle\Bus\Geo\Query\SearchCitiesQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="GeoBundle\Service\DTO\City")
     *     }
     * )
     */
    public function SearchCitiesAction(Request $request)
    {
        $query = new Query\SearchCitiesQuery($request->query->all());

        /** @var City[] $cities */
        $cities = $this->get('city.identity')->searchCity($query->q, $query->regionId, $query->limit);

        return new JsonResponse($cities);
    }

    /**
     * @VIA\Get(
     *     path="/geo/cities/{id}/info/",
     *     description="Получить информацию о городе по id",
     *     parameters={
     *          @VIA\Parameter(model="GeoBundle\Bus\Geo\Query\GetCityInfoQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="GeoBundle\Service\DTO\CityInfo")
     *     }
     * )
     */
    public function GetCityInfoAction(int $id, Request $request)
    {
        $query = new Query\GetCityInfoQuery($request->query->all(), ['id' => $id]);

        /** @var CityInfo $city */
        $city = $this->get('city.identity')->getCityInfo($query->id);

        if (!$city)
            throw new EntityNotFoundException('Нет такого города');

        return new JsonResponse($city);
    }

    /**
     * @VIA\Get(
     *     path="/geo/streets/foundResults/",
     *     description="Получить список улиц в городе для автокомплита",
     *     parameters={
     *          @VIA\Parameter(model="GeoBundle\Bus\Geo\Query\SearchStreetsQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="GeoBundle\Service\DTO\Street")
     *     }
     * )
     */
    public function SearchStreetsAction(Request $request)
    {
        $query = new Query\SearchStreetsQuery($request->query->all());

        /** @var Street[] $streets */
        $streets = $this->get('city.identity')->searchStreet($query->q, $query->cityId, $query->limit);

        return new JsonResponse($streets);
    }

    /**
     * @VIA\Get(
     *     path="/geo/streets/{id}/info/",
     *     description="Получить информацию об улице по id",
     *     parameters={
     *          @VIA\Parameter(model="GeoBundle\Bus\Geo\Query\GetStreetInfoQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="GeoBundle\Service\DTO\StreetInfo")
     *     }
     * )
     */
    public function GetStreetInfoAction(int $id, Request $request)
    {
        $query = new Query\GetStreetInfoQuery($request->query->all(), ['id' => $id]);

        /** @var StreetInfo $street */
        $street = $this->get('city.identity')->getStreetInfo($query->id);

        if (!$street)
            throw new EntityNotFoundException('Нет такой улицы');

        return new JsonResponse($street);
    }
}