<?php

namespace SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use SiteBundle\Bus\Catalog\Query;
use SiteBundle\Bus\Catalog\Paging;
use SiteBundle\Bus\Catalog\Sorting;
use SiteBundle\Bus\Catalog\Enum\Availability;
use SiteBundle\Bus\Catalog\Enum\Nofilled;
use SiteBundle\Bus\Catalog\Enum\Sort;
use SiteBundle\Bus\Catalog\Enum\SortDirection;

class SpecialsController extends Controller
{
    /**
     * @VIA\Get(
     *     name="catalog_specials",
     *     path="/specials/"
     * )
     * @VIA\Post(
     *     name="catalog_specials_post",
     *     path="/specials/"
     * )
     * @VIA\Response(status=200)
     */
    public function indexAction(Request $request)
    {
        if ($request->isMethod('POST')) {
            $data = $this->get('catalog.query_string')->fromPost($request->request->get('filter'), $request->query->all());

            $url = $this->generateUrl($request->attributes->get('_route'), $this->get('catalog.query_string')->build($data));

            if ($request->isXmlHttpRequest()) {
                $finder = $this->get('catalog.specials_product_finder')->setData($data);

                return $this->json([
                    'facets' => $finder->getFacets(),
                    'url' => $url,
                ]);
            }

            return $this->redirect($url);
        }

        $data = $this->get('catalog.query_string')->parse($request->query->all());
        $finder = $this->get('catalog.specials_product_finder')->setData($data);

        $filter = $finder->getFilter();
        $facets = $finder->getFacets();
        $productIds = $facets->total ? $finder->getProductIds() : [];
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
                'options' => Sort::getOptions($this->get('user.identity')->isEmployee()),
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
            $productsHtml = $this->renderView('SiteBundle:Catalog:products_list.html.twig', [
                'products' => $products,
            ]);
            $pagingHtml = $this->renderView('SiteBundle:Catalog:paging.html.twig', [
                'paging' => $paging,
            ]);
            $showmoreHtml = $this->renderView('SiteBundle:Catalog:showmore.html.twig', [
                'paging' => $paging,
            ]); 
            $sortingHtml = $this->renderView('SiteBundle:Catalog:sorting.html.twig', [
                'sorting' => $sorting,
            ]);

            return $this->json([
                'products' => $productsHtml,
                'paging' => $pagingHtml,
                'showmore' => $showmoreHtml,
                'sorting' => $sortingHtml,
            ]);
        }

        return $this->render('SiteBundle:Catalog:specials.html.twig', [
            'products' => $products,
            'filter' => $filter,
            'facets' => $facets,
            'data' => $data,
            'paging' => $paging,
            'sorting' => $sorting,
            'availabilityOptions' => Availability::getOptions($this->get('user.identity')->isEmployee()),
            'nofilledOptions' => Nofilled::getOptions(),   
            'resetUrl' => $this->generateUrl($request->attributes->get('_route')), 
        ]);
    }
}
