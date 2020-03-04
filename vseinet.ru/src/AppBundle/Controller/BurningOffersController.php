<?php

namespace AppBundle\Controller;

use AppBundle\Annotation as VIA;
use AppBundle\Bus\BurningOffers\Query;

class BurningOffersController extends Controller
{
    /**
     * @VIA\Get(name="burning_offers_page", path="/burningOffers/")
     */
    public function indexAction()
    {
        $products = $this->get('query_bus')->handle(new Query\GetListQuery());

        return $this->render('BurningOffers/index.html.twig', [
            'products' => $products,
        ]);
    }
}
