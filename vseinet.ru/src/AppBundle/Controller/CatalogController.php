<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use AppBundle\Bus\Catalog\Query;
use AppBundle\Bus\Brand\Query\GetByNameQuery as GetBrandByNameQuery;
use AppBundle\Bus\Cart\Query\GetInfoQuery as GetCartInfoQuery;

class CatalogController extends Controller
{
    /**
     * @VIA\Route(
     *     name="catalog_category",
     *     path="/catalog/{id}/",
     *     requirements={"id" = "\d*"},
     *     methods={"GET", "POST"},
     *     parameters={
     *         @VIA\Parameter(name="id", type="integer")
     *     }
     * )
     * @VIA\Route(
     *     name="catalog_category_with_brand",
     *     path="/catalog/{id}/{brandName}/",
     *     requirements={"id" = "\d*", "brandName" = "[^\/]*"},
     *     methods={"GET", "POST"},
     *     parameters={
     *         @VIA\Parameter(name="id", type="integer"),
     *         @VIA\Parameter(name="brandName", type="string")
     *     }
     * )
     */
    public function showCategoryPageAction(int $id = 0, $brandName = null, Request $request)
    {
        $brand = $brandName ? $this->get('query_bus')->handle(new GetBrandByNameQuery(['name' => $brandName])) : null;
        $category = $this->get('query_bus')->handle(new Query\GetCategoryQuery(['id' => $id, 'brand' => $brand]));

        $finder = $this->get('category.products.finder');
        $finder->setFilterData($request->query->all(), $category, $brand);
        exit;

        if ($request->isMethod('POST')) {
            if (!$category->isLeaf) {
                throw new BadRequestHttpException();
            }

            $finder->handleRequest($request->request->get('filter'));
            $filterUrl = $finder->getFilterUrl($brand ? 'catalog_category_with_brand' : 'catalog_category');

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

        if (1 === $finder->getParameter('page') && !empty($category->description)) {
            $category->image = $this->get('query_bus')->handle(new Query\GetCategoryImageQuery(['categoryId' => $category->id]));
        } else {
            $category->description = null;
        }

        return $this->show($request, $finder, ['category' => $category, 'brand' => $brand]);
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
        $finder = $this->get('specials.products.finder');
        $finder->setFilterData($request->query->all());

        if ($request->isMethod('POST')) {
            $finder->handleRequest($request->request->get('filter'));
            $filterUrl = $finder->getFilterUrl($request->attributes->get('_route'));

            if ($request->isXmlHttpRequest()) {
                return $this->json([
                    'facets' => $finder->getFacets(),
                    'filterUrl' => $filterUrl,
                ]);
            }

            return $this->redirect($filterUrl);
        }

        return $this->show($request, $finder);
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
        $finder = $this->get('search.products.finder');
        $finder->setFilterData($request->query->all());

        if ($request->isMethod('POST')) {
            $finder->handleRequest($request->request->get('filter'));
            $filterUrl = $finder->getFilterUrl($request->attributes->get('_route'));

            if ($request->isXmlHttpRequest()) {
                return $this->json([
                    'facets' => $finder->getFacets(),
                    'filterUrl' => $filterUrl,
                ]);
            }

            return $this->redirect($filterUrl);
        }

        return $this->show($request, $finder);
    }

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
    public function showBrandPageAction(string $name, Request $request)
    {
        $brand = $this->get('query_bus')->handle(new Query\GetBrandByNameQuery(['name' => $name]));
        if (null === $brand) {
            throw new NotFoundHttpException();
        }

        $finder = $this->get('brand.products.finder');
        $finder->setFilterData($request->query->all(), ['name' => $name]);

        if ($request->isMethod('POST')) {
            $finder->handleRequest($request->request->get('filter'));
            $filterUrl = $finder->getFilterUrl($request->attributes->get('_route'));

            if ($request->isXmlHttpRequest()) {
                return [
                    'facets' => $finder->getFacets(),
                    'filterUrl' => $filterUrl,
                ];
            }

            return $this->redirect($filterUrl);
        }

        return $this->show($request, $finder, ['brand' => $brand]);
    }

    /**
     * @VIA\Route(
     *     name="catalog_detail",
     *     path="/detail/{id}/",
     *     requirements={"id" = "\d+"},
     *     methods={"GET", "POST"}
     * )
     */
    public function showDetailPageAction(int $id, Request $request)
    {
        $detail = $this->get('query_bus')->handle(new Query\GetDetailQuery(['id' => $id]));

        $finder = $this->get('detail.products.finder');
        $finder->setFilterData($this->request->query->all(), ['id' => $id]);

        if ($request->isMethod('POST')) {
            $finder->handleRequest($request->request->get('filter'));
            $filterUrl = $finder->getFilterUrl($request->attributes->get('_route'));

            if ($request->isXmlHttpRequest()) {
                return [
                    'facets' => $finder->getFacets(),
                    'filterUrl' => $filterUrl,
                ];
            }

            return $this->redirect($filterUrl);
        }

        return $this->show($request, $finder, ['detail' => $detail]);
    }

    /**
     * @VIA\Route(
     *     name="catalog_supplier",
     *     path="/supplier/{code}/",
     *     requirements={"code" = "[^\/]*"},
     *     methods={"GET", "POST"}
     * )
     */
    public function showSupplierPageAction(string $code, Request $request)
    {
        $supplier = $this->get('query_bus')->handle(new Query\GetSupplierQuery(['code' => $code]));

        $finder = $this->get('supplier.products.finder');
        $finder->setFilterData($request->query->all(), ['code' => $code]);

        if ($request->isMethod('POST')) {
            $finder->handleRequest($request->request->get('filter'));
            $filterUrl = $finder->getFilterUrl($request->attributes->get('_route'));

            if ($request->isXmlHttpRequest()) {
                return $this->json([
                    'facets' => $finder->getFacets(),
                    'filterUrl' => $filterUrl,
                ]);
            }

            return $this->redirect($filterUrl);
        }

        return $this->show($request, $finder, ['supplier' => $supplier]);
    }

    protected function show(Request $request, Finder $finder, array $parameters = [])
    {
        $baseUrl = $finder->getBaseUrl($request->attributes->get('_route'));
        $filter = $finder->getFilter();
        $facets = $finder->getFacets();
        $products = $finder->getProducts();
        $paging = $finder->getPaging($baseUrl);
        $sorting = $finder->getSorting($baseUrl);


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

        return $this->render('Catalog/category.html.twig', $parameters + [
            'products' => $products,
            'filter' => $filter,
            'facets' => $facets,
            // 'data' => $data,
            'paging' => $paging,
            'sorting' => $sorting,
            'availabilityChoices' => $finder->getAvailabilityChoices(),
            'nofilledChoices' => $finder->getNofilledChoices(),
            // 'availabilityOptions' => Availability::getOptions($this->getUserIsEmployee()),
            // 'nofilledOptions' => Nofilled::getOptions(),
            'baseUrl' => $baseUrl,
        ]);
    }
}
