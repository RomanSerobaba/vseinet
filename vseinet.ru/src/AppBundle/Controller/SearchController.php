<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use AppBundle\Bus\Search\Query\GetCounterQuery;

class SearchController extends Controller
{


    /**
     * @VIA\Get(
     *     name="catalog_search_autocomplete",
     *     path="/search/autocomplete/",
     *     parameters={
     *         @VIA\Parameter(name="q", type="string", in="query", required=true)
     *     }
     * )
     */
    public function autocompleteAction(Request $request)
    {
        if (!$this->getUser() || $this->getUser()->getId() != 1503) {
            echo '<html><head><title>Интернет-магазин Vseinet.ru</title></head><body style="text-align:center;font-size:60px;margin-top:20%;">Извините за временные неудобства!<br/>На сайте ведутся технические работы.</body></html>';die();
        }
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();

        }
        $finder = $this->get('catalog.autocomplete.finder')->setFilterData($request->query->all());

        return $this->json([
            'result' => $finder->getResult(),
        ]);
    }

    /**
     * @internal
     */
    public function getPlaceholderAction()
    {
        if (!$this->get('request_stack')->getParentRequest() instanceof Request) {
            throw new NotFoundHttpException();
        }

        $counter = $this->get('query_bus')->handle(new GetCounterQuery());

        return $this->render('Search/placeholder.html.twig', [
            'counter' => $counter,
        ]);
    }
}
