<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use AppBundle\Bus\Catalog\Query;
use AppBundle\Bus\Brand\Query\GetBySefNameQuery as GetBrandBySefNameQuery;
use AppBundle\Bus\Brand\Query\GetByNameQuery as GetBrandByNameQuery;
use AppBundle\Bus\Brand\Query\GetByIdQuery as GetBrandByIdQuery;
use AppBundle\Bus\Catalog\Paging;
use AppBundle\Bus\Catalog\Sorting;
use AppBundle\Bus\Catalog\Enum\Availability;
use AppBundle\Bus\Catalog\Enum\Nofilled;
use AppBundle\Bus\Catalog\Enum\Sort;
use AppBundle\Bus\Main\Command\AddViewHistoryCategoryCommand;
use AppBundle\Bus\Main\Command\AddViewHistoryBrandCommand;
use AppBundle\Bus\Product\Query\GetLocalAvailabilityQuery;
use AppBundle\Entity\Category;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
     * @VIA\Route(
     *     name="catalog_category_sef_with_brand",
     *     path="/catalog/{slug}/b/{brandName}/",
     *     requirements={"slug": ".+-\d+$", "brandName": "[^\/]*"},
     *     methods={"GET", "POST"},
     *     parameters={
     *         @VIA\Parameter(name="slug", type="string"),
     *         @VIA\Parameter(name="brandName", type="string")
     *     }
     * )
     * @VIA\Route(
     *     name="catalog_category_sef",
     *     path="/catalog/{slug}/",
     *     requirements={"slug": ".+-\d+$"},
     *     methods={"GET", "POST"},
     *     parameters={
     *         @VIA\Parameter(name="slug", type="string")
     *     }
     * )
     */
    public function showCategoryPageAction(int $id = 0, $slug = null, $brandName = null, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $needRedirect = false;

        if ($brandName) {
            $brand = $this->get('query_bus')->handle(new GetBrandBySefNameQuery(['sefName' => $brandName]));
            if (!$brand) {
                $brand = $this->get('query_bus')->handle(new GetBrandByNameQuery(['name' => $brandName]));
                if (!$brand) {
                    return $this->redirectToRoute('catalog', [], 301);
                } elseif ($brand->sefName) {
                    $needRedirect = true;
                }
            }
            $request->query->set('b', $brand->id);
        } else {
            $brand = null;
        }

        if (!empty($id)) {
            $category = $em->getRepository(Category::class)->find($id);
            if (!$category) {
                return $this->redirectToRoute('catalog', [], 301);
            } elseif ($category->getSefUrl()) {
                $needRedirect = true;
            }
        }

        if ($slug) {
            $chunks = explode('-', $slug);
            $id = count($chunks) ? (int) end($chunks) : 0;

            $category = $em->getRepository(Category::class)->find($id);
            if (!$category) {
                return $this->redirectToRoute('index', [], 301);
            } elseif ($category->getSefUrl() && $category->getSefUrl() !== $slug) {
                $needRedirect = true;
            }
        }

        if ($needRedirect) {
            if ($category->getSefUrl()) {
                if ($brandName) {
                    return $this->redirectToRoute('catalog_category_sef_with_brand', $request->query->all() + ['slug' => $category->getSefUrl(), 'brandName' => $brand->sefName ? : $brand->name], 301);
                }

                return $this->redirectToRoute('catalog_category_sef', $request->query->all() + ['slug' => $category->getSefUrl()], 301);
            }

            if ($brandName) {
                return $this->redirectToRoute('catalog_category_with_brand', $request->query->all() + ['id' => $category->getId(), 'brandName' => $brand->sefName ? : $brand->name], 301);
            }

            return $this->redirectToRoute('catalog_category', $request->query->all() + ['id' => $category->getId()], 301);
        }

        $category = $this->get('query_bus')->handle(new Query\GetCategoryQuery(['id' => $id, 'brand' => $brand]));

        $finder = $this->get('catalog.category_product.finder');
        $finder->setFilterData($request->query->all() + ['id' => $id, 'brandName' => $brand->name ?? null], $category, $brand);

        if ($request->isMethod('POST')) {
            if (!$category->isLeaf) {
                throw new BadRequestHttpException('Категория не содержит товары');
            }

            $finder->handleRequest($request->request->get('filter'));

            $filter = $finder->getFilter();
            if (!empty($filter->brandIds) && 1 === count($filter->brandIds) && 0 < reset($filter->brandIds)) {
                $brand = $this->get('query_bus')->handle(new GetBrandByIdQuery(['id' => reset($filter->brandIds)]));
                $brandName = $brand->sefName ?? $brand->name;
                $route = 'catalog_category'.($category->sefUrl ? '_sef' : '').'_with_brand';
            } else {
                $brandName = null;
                $route = 'catalog_category'.($category->sefUrl ? '_sef' : '');
            }
            $filterUrl = $this->generateUrl($route, $filter->build(['slug' => $category->sefUrl, 'id' => $category->sefUrl ? null : $category->id, 'brandName' => $brandName]));

            if ($request->isXmlHttpRequest()) {
                $title = $category->name;
                if (null !== $brand->name) {
                    $title .= ' «'.$brand->name.'»';
                }

                return $this->json([
                    'facets' => $finder->getFacets(),
                    'filterUrl' => $filterUrl,
                    'title' => $title,
                ]);
            }

            return $this->redirect($filterUrl);
        }

        if (!$category->isLeaf) {
            $subcategories = $this->get('query_bus')->handle(new Query\GetSubcategoriesQuery(['pid' => $category->id]));

            return $this->render('Catalog/category_list.html.twig', [
                'category' => $category,
                'subcategories' => $subcategories,
            ]);
        }

        if (0 === $category->countProducts && !$this->getUserIsEmployee()) {
            return $this->redirectToRoute('catalog', [], 302);
        }

        if (1 === $finder->getFilter()->page && !empty($category->description)) {
            $category->image = $this->get('query_bus')->handle(new Query\GetCategoryImageQuery(['categoryId' => $category->id]));
        } else {
            $category->description = null;
        }

        $this->get('command_bus')->handle(new AddViewHistoryCategoryCommand(['categoryId' => $category->id]));
        if (null !== $brand) {
            $this->get('command_bus')->handle(new AddViewHistoryBrandCommand(['brandId' => $brand->id]));
        }

        return $this->show('category', $finder, $request, ['category' => $category, 'brand' => $brand], ['slug' => $slug, 'brandName' => $brandName]);
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
     *     name="catalog_total_sale",
     *     path="/total/sale/",
     *     methods={"GET", "POST"}
     * )
     */
    public function totalSaleAction(Request $request)
    {
        $finder = $this->get('catalog.total_sale_product.finder');
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

        return $this->show('total_sale', $finder, $request);
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

        $this->get('command_bus')->handle(new AddViewHistoryBrandCommand(['brandId' => $brand->id]));

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
        $finder->setFilterData($request->query->all(), $detail);

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
     *     name="catalog_detail_value",
     *     path="/detailValue/{id}/",
     *     requirements={"id": "\d+"},
     *     methods={"GET", "POST"}
     * )
     */
    public function showDetailValuePageAction(int $id, Request $request)
    {
        $value = $this->get('query_bus')->handle(new Query\GetDetailValueQuery(['id' => $id]));

        $finder = $this->get('catalog.detail_value_product.finder');
        $finder->setFilterData($request->query->all(), $value);

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

        return $this->show('detail_value', $finder, $request, ['value' => $value], ['id' => $id]);
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

        if ($this->getUserIsEmployee()) {
            $ids = array_map(function ($product) { return $product->id; }, $products);
            $geoPoints = [];
            foreach ($ids as $id) {
                $geoPoints[$id] = $this->get('query_bus')->handle(new GetLocalAvailabilityQuery(['baseProductId' => $id]));
            }
        }

        $route = $request->attributes->get('_route');
        $baseUrl = $this->generateUrl($route, $attributes);
        if ('catalog_category_sef_with_brand' === $route) {
            $route = 'catalog_category_sef';
            unset($attributes['brandName']);
        } elseif ('catalog_category_with_brand' === $route) {
            $route = 'catalog_category';
            unset($attributes['brandName']);
        }
        $resetUrl = $this->generateUrl($route, $attributes);

        $attributes = $filter->build();

        $paging = new Paging([
            'total' => $facets->total,
            'page' => $filter->page,
            'perpage' => $finder->getQueryBuilder()::PER_PAGE,
            'lines' => 4,
            'baseUrl' => $baseUrl,
            'resetUrl' => $resetUrl,
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
                'geoPoints' => $geoPoints ?? [],
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
            $mainCategoriesHtml = $this->renderView('Catalog/filter_main_categories.html.twig', [
                'features' => $finder->getFeatures(),
                'filter' => $filter,
                'facets' => $facets,
            ]);

            return $this->json([
                'products' => $productsHtml,
                'paging' => $pagingHtml,
                'showmore' => $showmoreHtml,
                'sorting' => $sortingHtml,
                'mainCategories' => $mainCategoriesHtml,
                'sort' => $filter->sort,
                'sortDirection' => $filter->sortDirection,
            ]);
        }

        return $this->render('Catalog/'.$view.'.html.twig', $parameters + [
            'features' => $finder->getFeatures(),
            'filter' => $filter,
            'facets' => $facets,
            'products' => $products,
            'geoPoints' => $geoPoints ?? [],
            'paging' => $paging,
            'sorting' => $sorting,
            'availabilityChoices' => Availability::getChoices($this->getUserIsEmployee()),
            'nofilledChoices' => Nofilled::getChoices(),
            'baseUrl' => $baseUrl,
            'resetUrl' => $resetUrl,
        ]);
    }
}
