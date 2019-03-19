<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use AppBundle\Bus\Catalog\Query;
use AppBundle\Bus\Brand\Query\GetByNameQuery as GetBrandByNameQuery;
use AppBundle\Bus\Brand\Query\GetByIdQuery as GetBrandByIdQuery;
use AppBundle\Bus\Catalog\Paging;
use AppBundle\Bus\Catalog\Sorting;
use AppBundle\Bus\Catalog\Enum\Availability;
use AppBundle\Bus\Catalog\Enum\Nofilled;
use AppBundle\Bus\Catalog\Enum\Sort;

class CatalogController extends Controller
{
    /**
     * @VIA\Route(
     *     name="catalog",
     *     path="/catalog/",
     *     methods={"GET", "POST"}
     * )
     * @VIA\Route(
     *     name="catalog_category",
     *     path="/catalog/{id}/",
     *     requirements={"id": "\d+"},
     *     methods={"GET", "POST"},
     *     parameters={
     *         @VIA\Parameter(name="id", type="integer")
     *     }
     * )
     * @VIA\Route(
     *     name="catalog_category_with_brand",
     *     path="/catalog/{id}/{brandName}/",
     *     requirements={"id": "\d+", "brandName": "[^\/]*"},
     *     methods={"GET", "POST"},
     *     parameters={
     *         @VIA\Parameter(name="id", type="integer"),
     *         @VIA\Parameter(name="brandName", type="string")
     *     }
     * )
     */
    public function showCategoryPageAction(int $id = 0, $brandName = null, Request $request)
    {
        if ($brandName) {
            $brand = $this->get('query_bus')->handle(new GetBrandByNameQuery(['name' => $brandName]));
            $request->query->set('b', $brand->id);
        } else {
            $brand = null;
        }
        $category = $this->get('query_bus')->handle(new Query\GetCategoryQuery(['id' => $id, 'brand' => $brand]));

        $finder = $this->get('catalog.category_product.finder');
        $finder->setFilterData($request->query->all(), $category, $brand);

        if ($request->isMethod('POST')) {
            if (!$category->isLeaf) {
                throw new BadRequestHttpException();
            }

            $finder->handleRequest($request->request->get('filter'));

            $filter = $finder->getFilter();
            if (!empty($filter->brandIds) && 1 === count($filter->brandIds) && 0 < reset($filter->brandIds)) {
                $brand = $this->get('query_bus')->handle(new GetBrandByIdQuery(['id' => reset($filter->brandIds)]));
                $brandName = $brand->name;
                $route = 'catalog_category_with_brand';
            } else {
                $brandName = null;
                $route = 'catalog_category';
            }
            $filterUrl = $this->generateUrl($route, $filter->build(['id' => $id, 'brandName' => $brandName]));

            if ($request->isXmlHttpRequest()) {
                return $this->json([
                    'facets' => $finder->getFacets(),
                    'filterUrl' => $filterUrl,
                ]);
            }

            return $this->redirect($filterUrl);
        }

        if (empty($category->pageTitle)) {
            $category->pageTitle = sprintf('Купить %s в Пензе, цена / Интернет-магазин "Vseinet.ru"', $category->name);
        }
        if (empty($category->pageDescription)) {
            $category->pageDescription = sprintf('В каталоге %s вы найдёте цены, отзывы, характеристики, описания и фотографии товаров. Наши цены вас порадуют!', $category->name);
        }

        if (!$category->isLeaf) {
            $subcategories = $this->get('query_bus')->handle(new Query\GetSubcategoriesQuery(['pid' => $category->id]));

            return $this->render('Catalog/category_list.html.twig', [
                'category' => $category,
                'subcategories' => $subcategories,
            ]);
        }

        if (0 === $category->countProducts && !$this->getUserIsEmployee()) {
            throw new NotFoundHttpException();
        }

        if (1 === $finder->getFilter()->page && !empty($category->description)) {
            $category->image = $this->get('query_bus')->handle(new Query\GetCategoryImageQuery(['categoryId' => $category->id]));
        } else {
            $category->description = null;
        }

        return $this->show('category', $finder, $request, ['category' => $category, 'brand' => $brand], ['id' => $id, 'brandName' => $brandName]);
    }

    /**
     * @VIA\Route(
     *     name="catalog_specials",
     *     path="/specials/",
     *     methods={"GET", "POST"}
     * )
     */
    public function showSpecialsPageAction(Request $request)
    {
        $finder = $this->get('catalog.special_product.finder');
        $finder->setFilterData($request->query->all());

        if ($request->isMethod('POST')) {
            $finder->handleRequest($request->request->get('filter'));
            $filterUrl = $this->generateUrl($request->attributes->get('_route'), $finder->getFilter()->build());

            if ($request->isXmlHttpRequest()) {
                return $this->json([
                    'facets' => $finder->getFacets(),
                    'filterUrl' => $filterUrl,
                ]);
            }

            return $this->redirect($filterUrl);
        }

        return $this->show('specials', $finder, $request);
    }

    /**
     * @VIA\Route(
     *     name="catalog_search",
     *     path="/search/",
     *     methods={"GET", "POST"}
     * )
     */
    public function showSearchPageAction(Request $request)
    {
        $finder = $this->get('catalog.search_product.finder');
        $finder->setFilterData($request->query->all());

        if ($request->isMethod('POST')) {
            $finder->handleRequest($request->request->get('filter'));
            $filterUrl = $this->generateUrl($request->attributes->get('_route'), $finder->getFilter()->build());

            if ($request->isXmlHttpRequest()) {
                return $this->json([
                    'facets' => $finder->getFacets(),
                    'filterUrl' => $filterUrl,
                ]);
            }

            return $this->redirect($filterUrl);
        }

        return $this->show('search', $finder, $request);
    }

    /**
     * @VIA\Route(
     *     name="catalog_brand",
     *     path="/brand/{name}/",
     *     requirements={"name": "[^\/]*"},
     *     parameters={
     *         @VIA\Parameter(name="name", type="string")
     *     },
     *     methods={"GET", "POST"}
     * )
     */
    public function showBrandPageAction(string $name, Request $request)
    {
        $brand = $this->get('query_bus')->handle(new GetBrandByNameQuery(['name' => $name]));
        if (null === $brand) {
            throw new NotFoundHttpException();
        }

        $finder = $this->get('catalog.brand_product.finder');
        $finder->setFilterData($request->query->all(), $brand);

        if ($request->isMethod('POST')) {
            $finder->handleRequest($request->request->get('filter'));
            $filterUrl = $this->generateUrl($request->attributes->get('_route'), $finder->getFilter()->build(['name' => $name]));

            if ($request->isXmlHttpRequest()) {
                return [
                    'facets' => $finder->getFacets(),
                    'filterUrl' => $filterUrl,
                ];
            }

            return $this->redirect($filterUrl);
        }

        return $this->show('brand', $finder, $request, ['brand' => $brand], ['name' => $name]);
    }

    /**
     * @VIA\Route(
     *     name="catalog_detail",
     *     path="/detail/{id}/",
     *     requirements={"id": "\d+"},
     *     methods={"GET", "POST"}
     * )
     */
    public function showDetailPageAction(int $id, Request $request)
    {
        $detail = $this->get('query_bus')->handle(new Query\GetDetailQuery(['id' => $id]));

        $finder = $this->get('catalog.detail_product.finder');
        $finder->setFilterData($this->request->query->all(), $detail);

        if ($request->isMethod('POST')) {
            $finder->handleRequest($request->request->get('filter'));
            $filterUrl = $this->generateUrl($request->attributes->get('_route'), $finder->getFilter()->build(['id' => $id]));

            if ($request->isXmlHttpRequest()) {
                return [
                    'facets' => $finder->getFacets(),
                    'filterUrl' => $filterUrl,
                ];
            }

            return $this->redirect($filterUrl);
        }

        return $this->show('detail', $finder, $request, ['detail' => $detail], ['id' => $id]);
    }

    /**
     * @VIA\Route(
     *     name="catalog_supplier",
     *     path="/supplier/{code}/",
     *     requirements={"code": "[^\/]*"},
     *     methods={"GET", "POST"}
     * )
     */
    public function showSupplierPageAction(string $code, Request $request)
    {
        $supplier = $this->get('query_bus')->handle(new Query\GetSupplierQuery(['code' => $code]));

        $finder = $this->get('catalog.supplier_product.finder');
        $finder->setFilterData($request->query->all(), $supplier);

        if ($request->isMethod('POST')) {
            $finder->handleRequest($request->request->get('filter'));
            $filterUrl = $this->generateUrl($request->attributes->get('_route'), $finder->getFilter()->build(['code' => $code]));

            if ($request->isXmlHttpRequest()) {
                return $this->json([
                    'facets' => $finder->getFacets(),
                    'filterUrl' => $filterUrl,
                ]);
            }

            return $this->redirect($filterUrl);
        }

        return $this->show('supplier', $finder, $request, ['supplier' => $supplier], ['code' => $code]);
    }

    protected function show(string $view, $finder, Request $request, array $parameters = [], array $attributes = [])
    {
        $filter = $finder->getFilter();
        $facets = $finder->getFacets();
        $products = $finder->getProducts();

        $baseUrl = $this->generateUrl($request->attributes->get('_route'), $attributes);
        $attributes = $filter->build();

        $paging = new Paging([
            'total' => $facets->total,
            'page' => $filter->page,
            'perpage' => $finder->getQueryBuilder()::PER_PAGE,
            'lines' => 8,
            'baseUrl' => $baseUrl,
            'attributes' => $attributes,
        ]);
        $sorting = new Sorting([
            'options' => Sort::getOptions($this->getUserIsEmployee()),
            'sort' => $filter->sort,
            'sortDirection' => $filter->sortDirection,
            'baseUrl' => $baseUrl,
            'attributes' => $attributes,
        ]);

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

        return $this->render('Catalog/'.$view.'.html.twig', $parameters + [
            'features' => $finder->getFeatures(),
            'filter' => $filter,
            'facets' => $facets,
            'products' => $products,
            'paging' => $paging,
            'sorting' => $sorting,
            'availabilityChoices' => Availability::getChoices($this->getUserIsEmployee()),
            'nofilledChoices' => Nofilled::getChoices(),
            'baseUrl' => $baseUrl,
        ]);
    }
}
