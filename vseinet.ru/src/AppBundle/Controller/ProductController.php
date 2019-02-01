<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use AppBundle\Annotation as VIA;
use AppBundle\Enum\ProductAvailabilityCode;
use AppBundle\Bus\Product\Query;
use AppBundle\Bus\Product\Command;
use AppBundle\Bus\Category\Query\GetBreadcrumbsQuery;
use AppBundle\Bus\Brand\Query\GetByIdQuery as GetBrandByIdQuery;
use AppBundle\Bus\Cart\Query\GetInfoQuery as GetCartInfoQuery;
use AppBundle\Bus\Favorite\Query\GetInfoQuery as GetFavoriteInfoQuery;
use AppBundle\Bus\Main\Command\AddLastviewProductCommand;
use AppBundle\Enum\DetailType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController extends Controller
{
    /**
     * @VIA\Get(
     *     name="catalog_product",
     *     path="/product/{id}/",
     *     requirements={"id" = "\d+"},
     *     parameters={
     *         @VIA\Parameter(name="id", type="integer")
     *     }
     * )
     * @VIA\Get(
     *     name="catalog_product_with_category",
     *     path="/product/{id}/{categoryId}/",
     *     requirements={"id" = "\d+", "categoryId" = "\d+"},
     *     parameters={
     *         @VIA\Parameter(name="id", type="integer"),
     *         @VIA\Parameter(name="categoryId", type="integer")
     *     }
     * )
     */
    public function indexAction(int $id, int $categoryId = null, Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetQuery(['id' => $id]), $product);

        if (null === $categoryId) {
            $categoryId = $product->categoryId;
        }
        $this->get('query_bus')->handle(new GetBreadcrumbsQuery(['categoryId' => $categoryId]), $breadcrumbs);

        if ($product->brandId) {
            $this->get('query_bus')->handle(new GetBrandByIdQuery(['id' => $product->brandId]), $brand);
        } else {
            $brand =  null;
        }

        $this->get('query_bus')->handle(new Query\GetImagesQuery(['baseProductId' => $product->id]), $images);

        $this->get('query_bus')->handle(new GetCartInfoQuery(), $cart);
        $product->quantityInCart = $cart->products[$product->id]->quantity ?? 0;

        $this->get('query_bus')->handle(new GetFavoriteInfoQuery(), $favorites);
        $product->inFavorites = in_array($product->id, $favorites->ids);

        $this->get('query_bus')->handle(new Query\GetLocalAvailabilityQuery(['baseProductId' => $product->id]), $points);

        $this->get('query_bus')->handle(new Query\GetDetailsQuery(['baseProductId' => $product->id]), $details);

        if (!empty($details)) {
            $count = 0;
            foreach ($details as $detail) {
                if (DetailType::CODE_ENUM === $detail->typeCode) {
                    continue;
                }
                $product->details[] = $detail;
                $count += 1;
                if (5 === $count) {
                    break;
                }
            }
        }

        $this->get('command_bus')->handle(new AddLastviewProductCommand(['baseProductId' => $product->id]));
        $cookie = new Cookie('products_lastview', $request->cookies->get('products_lastview'), time() + 3600 * 24 * 7);
        $response = new Response();
        $response->headers->setCookie($cookie);

        return $this->render('Product/index.html.twig', [
            'product' => $product,
            'breadcrumbs' => $breadcrumbs,
            'brand' => $brand,
            'images' => $images,
            'points' => $points,
            'details' => $details,
        ], $response);
    }

    /**
     * @VIA\Get(
     *     name="catalog_product_gallery",
     *     path="/product/gallary/{id}/",
     *     requirements={"id" = "\d+"},
     *     parameters={
     *         @VIA\Parameter(name="id", type="integer"),
     *         @VIA\Parameter(name="index", type="integer")
     *     },
     *     condition="request.isXmlHttpRequest()"
     * )
     */
    public function galleryAction(int $id, Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetQuery(['id' => $id]), $product);
        $this->get('query_bus')->handle(new Query\GetImagesQuery(['baseProductId' => $product->id]), $images);

        if (empty($images)) {
            throw new NotFoundHttpException();
        }

        return $this->json([
            'html' => $this->renderView('Product/gallery.html.twig', [
                'product' => $product,
                'images' => $images,
                'current' => $request->query->get('index', 0),
            ]),
        ]);
    }
}
