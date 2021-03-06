<?php

namespace AppBundle\Controller;

// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use AppBundle\Annotation as VIA;
use AppBundle\Bus\Product\Query;
use AppBundle\Bus\Category\Query\GetBreadcrumbsQuery;
use AppBundle\Bus\Brand\Query\GetByIdQuery as GetBrandByIdQuery;
use AppBundle\Bus\Cart\Query\GetInfoQuery as GetCartInfoQuery;
use AppBundle\Bus\Favorite\Query\GetInfoQuery as GetFavoriteInfoQuery;
use AppBundle\Bus\Main\Command\AddLastviewProductCommand;
use AppBundle\Enum\DetailType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Enum\ProductAvailabilityCode;

class ProductController extends Controller
{
    /**
     * @VIA\Get(
     *     name="catalog_product",
     *     path="/product/{id}/",
     *     requirements={"id"="\d+"},
     *     parameters={
     *         @VIA\Parameter(name="id", type="integer")
     *     }
     * )
     * @VIA\Get(
     *     name="catalog_product_with_category",
     *     path="/product/{id}/{categoryId}/",
     *     requirements={"id"="\d+", "categoryId"="\d+"},
     *     parameters={
     *         @VIA\Parameter(name="id", type="integer"),
     *         @VIA\Parameter(name="categoryId", type="integer")
     *     }
     * )
     */
    public function indexAction(int $id, int $categoryId = null, Request $request)
    {
        $baseProduct = $this->get('query_bus')->handle(new Query\GetQuery(['id' => $id]));

        if (null === $categoryId) {
            $categoryId = $baseProduct->categoryId;
        }
        $breadcrumbs = $this->get('query_bus')->handle(new GetBreadcrumbsQuery(['categoryId' => $categoryId]));

        if ($baseProduct->brandId) {
            $brand = $this->get('query_bus')->handle(new GetBrandByIdQuery(['id' => $baseProduct->brandId]));
        }

        $images = $this->get('query_bus')->handle(new Query\GetImagesQuery(['baseProductId' => $baseProduct->id]));

        $cart = $this->get('query_bus')->handle(new GetCartInfoQuery());
        $baseProduct->quantityInCart = $cart->products[$baseProduct->id]->quantity ?? 0;

        $favorites = $this->get('query_bus')->handle(new GetFavoriteInfoQuery());
        $baseProduct->inFavorites = in_array($baseProduct->id, $favorites->ids);

        $details = $this->get('query_bus')->handle(new Query\GetDetailsQuery(['baseProductId' => $baseProduct->id]));
        if (!empty($details)) {
            $count = 0;
            foreach ($details as $detail) {
                if (DetailType::CODE_MEMO === $detail->typeCode) {
                    continue;
                }
                $baseProduct->details[] = $detail;
                ++$count;
                if (5 === $count) {
                    break;
                }
            }
        }

        if (ProductAvailabilityCode::AVAILABLE === $baseProduct->availability || $this->getUserIsEmployee()) {
            $geoPoints = $this->get('query_bus')->handle(new Query\GetLocalAvailabilityQuery(['baseProductId' => $baseProduct->id]));
        }
        if (in_array($baseProduct->availability, [ProductAvailabilityCode::ON_DEMAND, ProductAvailabilityCode::IN_TRANSIT])) {
            $delivery = $this->get('query_bus')->handle(new Query\GetDeliveryDateQuery(['baseProductIds' => [$baseProduct->id]]))[$baseProduct->id];
        }

        $this->get('command_bus')->handle(new AddLastviewProductCommand(['baseProductId' => $baseProduct->id]));
        $cookie = new Cookie('products_lastview', $request->cookies->get('products_lastview'), time() + 3600 * 24 * 7);
        $response = new Response();
        $response->headers->setCookie($cookie);

        return $this->render('Product/index.html.twig', [
            'baseProduct' => $baseProduct,
            'breadcrumbs' => $breadcrumbs,
            'brand' => $brand ?? null,
            'images' => $images,
            'details' => $details,
            'geoPoints' => $geoPoints ?? null,
            'delivery' => $delivery ?? null,
        ], $response);
    }

    /**
     * @VIA\Get(
     *     name="catalog_product_gallery",
     *     path="/product/gallary/{id}/",
     *     requirements={"id"="\d+"},
     *     parameters={
     *         @VIA\Parameter(name="id", type="integer"),
     *         @VIA\Parameter(name="index", type="integer")
     *     },
     *     condition="request.isXmlHttpRequest()"
     * )
     */
    public function galleryAction(int $id, Request $request)
    {
        $product = $this->get('query_bus')->handle(new Query\GetQuery(['id' => $id]));
        $images = $this->get('query_bus')->handle(new Query\GetImagesQuery(['baseProductId' => $product->id]));

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
