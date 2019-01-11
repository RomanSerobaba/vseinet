<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use AppBundle\Bus\Catalog\Query;
use AppBundle\Bus\Brand\Query\GetByIdQuery as GetBrandByIdQuery;
use AppBundle\Bus\Brand\Query\GetByNameQuery as GetBrandByNameQuery;
use AppBundle\Bus\Cart\Query\GetInfoQuery as GetCartInfoQuery;
use AppBundle\Bus\Catalog\Paging;
use AppBundle\Bus\Catalog\Sorting;
use AppBundle\Bus\Catalog\Enum\Availability;
use AppBundle\Bus\Catalog\Enum\Nofilled;
use AppBundle\Bus\Catalog\Enum\Sort;
use AppBundle\Bus\Catalog\Enum\SortDirection;

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
    public function indexAction(int $id = 0, $brandName = null, Request $request)
    {
        if (null !== $brandName) {
            $this->get('query_bus')->handle(new GetBrandByNameQuery(['name' => $brandName]), $brand);
        } else {
            $brand = null;
        }
        $this->get('query_bus')->handle(new Query\GetCategoryQuery(['id' => $id, 'brand' => $brand]), $category);

        if ($request->isMethod('POST')) {
            if ($category->isLeaf) {
                $data = $this->get('catalog.query_string')->fromPost($request->request->get('filter'), $request->query->all());

                $route = $request->attributes->get('_route');
                if (!empty($data->brandIds)) {
                    if (1 === count($data->brandIds) && -1 < reset($data->brandIds)) {
                        $this->get('query_bus')->handle(new GetBrandByIdQuery(['id' => reset($data->brandIds)]), $brand);
                        $data->brandIds = null;
                        $brandName = $brand->name;
                        $route = 'catalog_category_with_brand';
                    } else {
                        $brandName = null;
                        $route = 'catalog_category';
                        $brand = null;
                    }
                } else {
                    $brandName = null;
                    $route = 'catalog_category';
                    $brand = null;
                }

                $url = $this->generateUrl(
                    $route,
                    array_merge(
                        $this->get('catalog.query_string')->build($data),
                        ['id' => $id, 'brandName' => $brandName]
                    )
                );

                if ($request->isXmlHttpRequest()) {
                    $finder = $this->get('catalog.category_product_finder')->setCategory($category)->setBrand($brand)->setData($data);

                    return $this->json([
                        'facets' => $finder->getFacets(),
                        'url' => $url,
                    ]);
                }

                return $this->redirect($url);
            }

            throw new NotFoundHttpException();
        }

        if (empty($category->pageTitle)) {
            $category->pageTitle = sprintf('Купить %s в Пензе, цена / Интернет-магазин "Vseinet.ru"', $category->name);
        }
        if (empty($category->pageDescription)) {
            $category->pageDescription = sprintf('В каталоге %s вы найдёте цены, отзывы, характеристики, описания и фотографии товаров. Наши цены вас порадуют!', $category->name);
        }

        if (!$category->isLeaf) {
            $this->get('query_bus')->handle(new Query\GetSubcategoriesQuery(['pid' => $category->id]), $subcategories);

            return $this->render('Catalog/category_list.html.twig', [
                'category' => $category,
                'subcategories' => $subcategories,
            ]);
        }

        if (0 === $category->countProducts && !$this->getUserIsEmployee()) {
            throw new NotFoundHttpException();
        }

        $data = $this->get('catalog.query_string')->parse($request->query->all());
        $finder = $this->get('catalog.category_product_finder')->setCategory($category)->setBrand($brand)->setData($data);

        if (1 !== $data->page) {
            $category->description = null;
        }
        if ($category->description) {
            $this->get('query_bus')->handle(new Query\GetCategoryImageQuery(['categoryId' => $category->id]), $category->image);
        }

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
                'baseUrl' => $this->generateUrl($request->attributes->get('_route'), ['id' => $id, 'brandName' => $brandName]),
                'attributes' => $this->get('catalog.query_string')->build($data),
            ]);
            $sorting = new Sorting([
                'options' => Sort::getOptions($this->getUserIsEmployee()),
                'sort' => $data->sort,
                'sortDirection' => $data->sortDirection,
                'baseUrl' => $this->generateUrl($request->attributes->get('_route'), ['id' => $id, 'brandName' => $brandName]),
                'attributes' => $this->get('catalog.query_string')->build($data),
            ]);
            $this->get('query_bus')->handle(new GetCartInfoQuery(), $info);
            array_walk($products, function(&$product) use ($info) {
                $product->quantityInCart = $info->products[$product->id]->quantity ?? 0;
            });
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

        if (null !== $brand) {
            $data->brandIds[$brand->id] = $brand->id;
        }

        return $this->render('Catalog/category.html.twig', [
            'category' => $category,
            'brand' => $brand,
            'products' => $products,
            'filter' => $filter,
            'facets' => $facets,
            'data' => $data,
            'paging' => $paging,
            'sorting' => $sorting,
            'availabilityOptions' => Availability::getOptions($this->getUserIsEmployee()),
            'nofilledOptions' => Nofilled::getOptions(),
            'resetUrl' => $this->generateUrl($request->attributes->get('_route'), ['id' => $id, 'brandName' => $brandName]),
        ]);
    }
}
