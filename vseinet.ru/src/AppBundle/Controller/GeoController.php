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
     *     name="geo_regions",
     *     path="geo/regions/",
     *     condition="request.isXmlHttpRequest()"
     * )
     */
    public function getRegionsAction()
    {
        $this->get('query_bus')->handle(new Query\GetRegionsQuery(), $regions);

        return $this->json([
            'html' => $this->renderView('Geo/regions.html.twig', [
                'regions' => $regions,
            ]),
        ]);
    }

    /**
     * @VIA\Get(
     *     name="geo_cities",
     *     path="/geo/cities/",
     *     parameters={
     *         @VIA\Parameter(name="regionId", type="integer")
     *     },
     *     condition="request.isXmlHttpRequest()"
     * )
     */
    public function getCitiesAction(int $regionId)
    {
        $this->get('query_bus')->handle(new Query\GetCitiesQuery(['regionId' => $regionId]), $cities);

        return $this->json([
            'html' => $this->renderView('Geo/cities.html.twig', [
                'cities' => $cities,
            ]),
        ]);
    }

    /**
     * @VIA\Post(
     *     name="set_geo_city",
     *     path="/cities/",
     *     parameters={
     *         @VIA\Parameter(name="id", type="integer")
     *     },
     *     condition="request.isXmlHttpRequest()"
     * )
     */
    public function setGeoCityCurrentAction(int $id)
    {
        $this->get('command_bus')->handle(new Command\SetGeoCityCurrentCommand(['id' => $id]));

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
        $this->get('query_bus')->handle(new Query\GetContactsQuery(), $contacts);

        return $this->render('Geo/contacts.html.smarty', [
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

    }
}
