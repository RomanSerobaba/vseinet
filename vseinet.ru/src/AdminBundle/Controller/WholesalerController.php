<?php

namespace AdminBundle\Controller;

use AppBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Annotation as VIA;
use AdminBundle\Bus\Wholesaler\Query;

/**
 * @Security("is_granted('ROLE_EMPLOYEE')")
 */
class WholesalerController extends Controller
{
    /**
     * @VIA\Get(
     *     name="admin_wholesaler_prices",
     *     path="/wholesaler/prices/",
     *     parameters={
     *         @VIA\Parameter(model="AdminBundle\Bus\Wholesaler\Query\GetPricesQuery")
     *     },
     *     condition="request.isXmlHttpRequest()"
     * )
     */
    public function pricesAction(Request $request)
    {
        $prices = $this->get('query_bus')->handle(new Query\GetPricesQuery($request->query->all()));

        return $this->json([
            'html' => $this->renderView('@Admin/Wholesaler/prices.html.twig', [
                'prices' => $prices,
            ]),
        ]);
    }
}
