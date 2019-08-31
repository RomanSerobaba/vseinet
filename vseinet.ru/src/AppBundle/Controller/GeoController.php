<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use AppBundle\Bus\Geo\Command;
use AppBundle\Bus\Geo\Query;

class GeoController extends Controller
{
    /**
     * @VIA\Get(
     *     name="geo_cities",
     *     path="/geo/cities/",
     *     condition="request.isXmlHttpRequest()"
     * )
     */
    public function citiesAction()
    {
        if (!$this->getUser() || $this->getUser()->getId() != 1503) {
            echo '<html><head><title>Интернет-магазин Vseinet.ru</title></head><body style="text-align:center;font-size:60px;margin-top:20%;">Извините за временные неудобства!<br/>На сайте ведутся технические работы.</body></html>';die();
        }
        $geoRegions = $this->get('query_bus')->handle(new Query\GetRegionsQuery());
        $data = $this->get('query_bus')->handle(new Query\GetCitiesQuery(['geoRegionId' => $this->getGeoCity()->getGeoRegionId()]));

        return $this->json([
            'html' => $this->renderView('Geo/cities.html.twig', $data + [
                'geoRegions' => $geoRegions,
            ]),
        ]);
    }

    /**
     * @VIA\Post(
     *     name="search_geo_city",
     *     path="/geo/cities/search/",
     *     condition="request.isXmlHttpRequest()"
     * )
     */
    public function searchAction(Request $request)
    {
        if (!$this->getUser() || $this->getUser()->getId() != 1503) {
            echo '<html><head><title>Интернет-магазин Vseinet.ru</title></head><body style="text-align:center;font-size:60px;margin-top:20%;">Извините за временные неудобства!<br/>На сайте ведутся технические работы.</body></html>';die();
        }
        $geoCities = $this->get('query_bus')->handle(new Query\SearchCityQuery($request->request->all()));

        return $this->json([
            'geoCities' => $geoCities,
        ]);
    }

    /**
     * @VIA\Post(
     *     name="search_geo_street",
     *     path="/geo/streets/search/",
     *     condition="request.isXmlHttpRequest()"
     * )
     */
    public function searchStreetAction(Request $request)
    {
        if (!$this->getUser() || $this->getUser()->getId() != 1503) {
            echo '<html><head><title>Интернет-магазин Vseinet.ru</title></head><body style="text-align:center;font-size:60px;margin-top:20%;">Извините за временные неудобства!<br/>На сайте ведутся технические работы.</body></html>';die();
        }
        $geoStreets = $this->get('query_bus')->handle(new Query\SearchStreetQuery($request->request->all()));

        return $this->json([
            'geoStreets' => $geoStreets,
        ]);
    }

    /**
     * @VIA\Post(
     *     name="select_geo_region",
     *     path="/geo/regions/",
     *     parameters={
     *         @VIA\Parameter(model="AppBundle\Bus\Geo\Query\GetCitiesQuery")
     *     },
     *     condition="request.isXmlHttpRequest()"
     * )
     */
    public function selectGeoRegionAction(Request $request)
    {
        if (!$this->getUser() || $this->getUser()->getId() != 1503) {
            echo '<html><head><title>Интернет-магазин Vseinet.ru</title></head><body style="text-align:center;font-size:60px;margin-top:20%;">Извините за временные неудобства!<br/>На сайте ведутся технические работы.</body></html>';die();
        }
        $data = $this->get('query_bus')->handle(new Query\GetCitiesQuery($request->request->all()));

        return $this->json([
            'html' => $this->renderView('Geo/cities_block.html.twig', $data),
        ]);
    }

    /**
     * @VIA\Post(
     *     name="select_geo_city",
     *     path="/geo/cities/",
     *     parameters={
     *         @VIA\Parameter(model="AppBundle\Bus\Geo\Command\SetGeoCityCurrentCommand")
     *     },
     *     condition="request.isXmlHttpRequest()"
     * )
     */
    public function selectGeoCityAction(Request $request)
    {
        if (!$this->getUser() || $this->getUser()->getId() != 1503) {
            echo '<html><head><title>Интернет-магазин Vseinet.ru</title></head><body style="text-align:center;font-size:60px;margin-top:20%;">Извините за временные неудобства!<br/>На сайте ведутся технические работы.</body></html>';die();
        }
        $this->get('command_bus')->handle(new Command\SetCityCurrentCommand($request->request->all()));

        return $this->json([
            'notice' => 'Город изменен',
        ]);
    }

    /**
     * @VIA\Get(
     *     name="contacts_page",
     *     path="/contacts/"
     * )
     */
    public function getContactsAction()
    {
        if (!$this->getUser() || $this->getUser()->getId() != 1503) {
            echo '<html><head><title>Интернет-магазин Vseinet.ru</title></head><body style="text-align:center;font-size:60px;margin-top:20%;">Извините за временные неудобства!<br/>На сайте ведутся технические работы.</body></html>';die();
        }
        $contacts = $this->get('query_bus')->handle(new Query\GetContactsQuery());

        return $this->render('Geo/contacts.html.twig', [
            'contacts' => $contacts,
        ]);
    }

    /**
     * @VIA\Get(
     *     name="contacts_representative",
     *     path="/contacts/{geoPointId}/",
     *     requirements={"geoPointId": "\d+"}
     * )
     */
    public function getContactAction(int $geoPointId, Request $request)
    {
        if (!$this->getUser() || $this->getUser()->getId() != 1503) {
            echo '<html><head><title>Интернет-магазин Vseinet.ru</title></head><body style="text-align:center;font-size:60px;margin-top:20%;">Извините за временные неудобства!<br/>На сайте ведутся технические работы.</body></html>';die();
        }
        $representative = $this->get('query_bus')->handle(new Query\GetRepresentativeQuery(['geoPointId' => $geoPointId]));

        if ($request->isXmlHttpRequest()) {
            return $this->json([
                'html' => $this->renderView('Geo/representative_short.html.twig', [
                    'representative' => $representative,
                ]),
            ]);
        }

        return $this->render('Geo/representative.html.twig', [
            'representative' => $representative,
        ]);
    }
}
