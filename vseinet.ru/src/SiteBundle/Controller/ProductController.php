<?php

namespace SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use AppBundle\Annotation as VIA;
use AppBundle\Enum\ProductAvailabilityCode;
use SiteBundle\Bus\Product\Query;
use SiteBundle\Bus\Product\Command;
use SiteBundle\Bus\Category\Query\GetBreadcrumbsQuery;
use SiteBundle\Bus\Brand\Query\GetByIdQuery as GetBrandByIdQuery;
use SiteBundle\Bus\Cart\Query\GetInfoQuery as GetCartInfoQuery;
use SiteBundle\Bus\Favorite\Query\GetInfoQuery as GetFavoriteInfoQuery;
use SiteBundle\Bus\Main\Command\AddLastviewProductCommand;
use AppBundle\Enum\BaseProductImage;
use AppBundle\Enum\DetailType;

class ProductController extends Controller
{
    /**
     * @VIA\Get(
     *     name="catalog_product",
     *     path="/product/{id}/",
     *     requirements={"id" = "\d+"}
     * )    
     * @VIA\Get(
     *     name="catalog_product_with_category",
     *     path="/product/{id}/{categoryId}/",
     *     requirements={"id" = "\d+", "categoryId" = "\d+"}
     * )
     * @VIA\Response(status=200)
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


        // print_r($details);
        // exit;

        // print_r($product); 
        // print_r($breadcrumbs);
        // print_r($brand);
        // print_r($images);
        // print_r($points);
        // exit;

        return $this->render('SiteBundle:Product:index.html.twig', [
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
     *     condition="request.isXmlHttpRequest()"
     * )
     * @VIA\Response(status=200)
     */
    public function galleryAction(int $id, Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetQuery(['id' => $id]), $product);
        $this->get('query_bus')->handle(new Query\GetImagesQuery(['baseProductId' => $product->id]), $images);
        if (empty($images)) {
            throw new NotFoundHttpException();
        }

        return $this->json([
            'html' => $this->renderView('SiteBundle:Product:gallery.html.twig', [
                'product' => $product,
                'images' => $images,
                'current' => $request->query->get('index', 0),
            ]),
        ]);
    }
}
