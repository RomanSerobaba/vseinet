<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use AppBundle\Entity\BaseProduct;
use AppBundle\Bus\Catalog\Query;
use AppBundle\Bus\Search\Query\GetCounterQuery;
use AppBundle\Bus\Catalog\Paging;
use AppBundle\Bus\Catalog\Sorting;
use AppBundle\Bus\Catalog\Enum\Availability;
use AppBundle\Bus\Catalog\Enum\Nofilled;
use AppBundle\Bus\Catalog\Enum\Sort;
use AppBundle\Bus\Catalog\Enum\SortDirection;

class SearchController extends Controller
{
    /**
     * @VIA\Route(
     *     name="catalog_search", 
     *     path="/search/", 
     *     methods={"GET", "POST"}
     * )
     */
    public function indexAction(Request $request)
    {
        if ($request->isMethod('POST')) {
            $data = $this->get('catalog.query_string')->fromPost($request->request->get('filter'), $request->query->all());
            $url = $this->generateUrl($request->attributes->get('_route'), $this->get('catalog.query_string')->build($data));

            if ($request->isXmlHttpRequest()) {
                $finder = $this->get('catalog.search_product_finder')->setData($data);

                return $this->json([
                    'facets' => $finder->getFacets(),
                    'url' => $url,
                ]);
            }

            return $this->redirect($url);
        }

        $data = $this->get('catalog.query_string')->parse($request->query->all());
        $finder = $this->get('catalog.search_product_finder')->setData($data);

        $filter = $finder->getFilter();
        $facets = $finder->getFacets();
        $productIds = $facets->total ? $finder->getProductIds() : [];
        $product = $this->getDoctrine()->getManager()->getRepository(BaseProduct::class)->find($data->q);
        if ($product instanceof BaseProduct) {
            array_unshift($productIds, $product->getId());
        }
        if (!empty($productIds)) {
            $this->get('query_bus')->handle(new Query\GetProductsQuery(['ids' => $productIds]), $products);
            $paging = new Paging([
                'total' => $facets->total,
                'page' => $data->page,
                'perpage' => $finder::PER_PAGE,
                'lines' => 8,
                'baseUrl' => $this->generateUrl($request->attributes->get('_route')),
                'attributes' => $this->get('catalog.query_string')->build($data),
            ]);
            $sorting = new Sorting([
                'options' => Sort::getOptions($this->getUserIsEmployee()),
                'sort' => $data->sort,
                'sortDirection' => $data->sortDirection,
                'baseUrl' => $this->generateUrl($request->attributes->get('_route')),
                'attributes' => $this->get('catalog.query_string')->build($data),
            ]);
        } else {
            $products = [];
            $paging = null;
            $sorting = null;
        }

        if ($request->isXmlHttpRequest()) {
            $productsHtml = $this->renderView('Catalog/products_list.html.twig', [
                'products' => $products,
            ]);
            $pagingHtml = $this->renderView('Catalog/paging.html.twig', [
                'paging' => $paging,
            ]);
            $showmoreHtml = $this->renderView('Catalog/showmore.html.twig', [
                'paging' => $paging,
            ]); 
            $sortingHtml = $this->renderView('Catalog/sorting.html.twig', [
                'sorting' => $sorting,
            ]);

            return $this->json([
                'products' => $productsHtml,
                'paging' => $pagingHtml,
                'showmore' => $showmoreHtml,
                'sorting' => $sortingHtml,
            ]);
        }

        return $this->render('Catalog/search.html.twig', [
            'products' => $products,
            'filter' => $filter,
            'facets' => $facets,
            'data' => $data,
            'paging' => $paging,
            'sorting' => $sorting,
            'availabilityOptions' => Availability::getOptions($this->getUserIsEmployee()),
            'nofilledOptions' => Nofilled::getOptions(),   
            'resetUrl' => $this->generateUrl($request->attributes->get('_route')), 
        ]);
    }

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
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
            
        }    
        $data = $this->get('catalog.query_string')->parse($request->query->all());
        $finder = $this->get('catalog.autocomplete_finder')->setData($data);

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

        $this->get('query_bus')->handle(new GetCounterQuery(), $counter);

        return $this->render('Search/placeholder.html.twig', [
            'counter' => $counter,
        ]);
    }
}
