<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
        $geoRegions = $this->get('query_bus')->handle(new Query\GetRegionsQuery());
        $geoRegionId = $this->getGeoCity()->getGeoRegionId();
        $data = $this->get('query_bus')->handle(new Query\GetCitiesQuery(['geoRegionId' => $geoRegionId]));

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
        $contacts = $this->get('query_bus')->handle(new Query\GetContactsQuery());

        return $this->render('Geo/contacts.html.twig', [
            'contacts' => $contacts,
        ]);
    }

    /**
     * @VIA\Get(
     *     name="contacts_representative",
     *     path="/contacts/{geoPointId}/",
     *     requirements={"geoPointId" = "\d+"}
     * )
     */
    public function getContactAction(int $geoPointId, Request $request)
    {
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
