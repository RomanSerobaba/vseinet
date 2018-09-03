<?php 

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Annotation as VIA;
use AppBundle\Bus\Brand\Query\GetByNameQuery;
use AppBundle\Bus\Catalog\Query\GetProductsQuery;
use AppBundle\Bus\Catalog\Paging;
use AppBundle\Bus\Catalog\Sorting;
use AppBundle\Bus\Catalog\Enum\Availability;
use AppBundle\Bus\Catalog\Enum\Nofilled;
use AppBundle\Bus\Catalog\Enum\Sort;
use AppBundle\Bus\Catalog\Enum\SortDirection;

class BrandController extends Controller
{
    /**
     * @VIA\Route(
     *     name="catalog_brand", 
     *     path="/brand/{name}/", 
     *     requirements={"name" = "[^\/]*"}, 
     *     parameters={
     *         @VIA\Parameter(name="name", type="string")     
     *     },
     *     methods={"GET", "POST"}
     * )
     */
    public function indexAction(string $name, Request $request)
    {
        $this->get('query_bus')->handle(new GetByNameQuery(['name' => $name]), $brand);
        
        if ($request->isMethod('POST')) {
            $data = $this->get('catalog.query_string')->fromPost($request->request->get('filter'), $request->query->all());

            $url = $this->generateUrl(
                $request->attributes->get('_route'), 
                array_merge($this->get('catalog.query_string')->build($data), ['name' => $name])
            );

            if ($request->isXmlHttpRequest()) {
                $finder = $this->get('catalog.brand_product_finder')->setBrand($brand)->setData($data);

                return $this->json([
                    'facets' => $finder->getFacets(),
                    'url' => $url,
                ]);
            }

            return $this->redirect($url);
        }

        $data = $this->get('catalog.query_string')->parse($request->query->all());
        $finder = $this->get('catalog.brand_product_finder')->setBrand($brand)->setData($data);

        $filter = $finder->getFilter();
        $facets = $finder->getFacets();
        $productIds = $facets->total ? $finder->getProductIds() : [];
        if (!empty($productIds)) {
            $this->get('query_bus')->handle(new GetProductsQuery(['ids' => $productIds]), $products);
            $paging = new Paging([
                'total' => $facets->total,
                'page' => $data->page,
                'perpage' => $finder::PER_PAGE,
                'lines' => 8,
                'baseUrl' => $this->generateUrl($request->attributes->get('_route'), ['name' => $name]),
                'attributes' => $this->get('catalog.query_string')->build($data),
            ]);
            $sorting = new Sorting([
                'options' => Sort::getOptions($this->getUserIsEmployee()),
                'sort' => $data->sort,
                'sortDirection' => $data->sortDirection,
                'baseUrl' => $this->generateUrl($request->attributes->get('_route'), ['name' => $name]),
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

        return $this->render('Catalog/brand.html.twig', [
            'brand' => $brand,
            'products' => $products,
            'filter' => $filter,
            'facets' => $facets,
            'data' => $data,
            'paging' => $paging,
            'sorting' => $sorting,
            'availabilityOptions' => Availability::getOptions($this->getUserIsEmployee()),
            'nofilledOptions' => Nofilled::getOptions(),   
            'resetUrl' => $this->generateUrl($request->attributes->get('_route'), ['name' => $name]), 
        ]);
    }
}
