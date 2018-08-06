<?php 

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use AppBundle\Bus\Geo\Query;

class GeoController extends Controller
{
    /**
     * @VIA\Get(
     *     name="search_city", 
     *     path="/cities/foundResults/", 
     *     condition="request.isXmlHttpRequest()",
     *     parameters={
     *         @VIA\Parameter(model="AppBundle\Bus\Geo\Query\SearchCityQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="AppBundle\Bus\Geo\CityFound")
     *     }
     * )
     */
    public function searchAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\SearchCityQuery($request->query->all()), $cities);

        return $this->json([
            'cities' => $cities,
        ]);
    }
}
