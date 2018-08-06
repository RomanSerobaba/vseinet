<?php

namespace SiteBundle\Controller;

use AppBundle\Annotation as VIA;
use SiteBundle\Bus\Geo\Command;
use SiteBundle\Bus\Geo\Query;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GeoController extends Controller
{
    /**
     * @VIA\Get(
     *     name="geo_cities",
     *     path="/cities/",
     *     parameters={
     *         @VIA\Parameter(name="regionId", type="integer")
     *     }
     * )
     */
    public function getCitiesAction(int $regionId)
    {
        $this->get('query_bus')->handle(new Query\GetCitiesQuery(['regionId' => $regionId]), $cities);

        return $this->render('SiteBundle:Geo:cities.html.smarty', [
            'cities' => $cities,
        ]);
    }

    /**
     * @VIA\Post(
     *     path="/cities/{id}/",
     *     parameters={
     *         @VIA\Parameter(name="id", type="integer")
     *     }
     * )
     */
    public function setCityCurrentAction(int $id, Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        $this->get('command_bus')->handle(new Command\SetCityCurrentCommand(['id' => $id]));
    }
}