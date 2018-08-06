<?php

namespace SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;

class CheaperController extends Controller
{
    /**
     * @VIA\Get(
     *     name="catalog_product_cheaper",
     *     path="/cheaper/{id}/",
     *     requirements={"id" = "\d+"}
     * )    
     * @VIA\Get(
     *     name="catalog_product_cheaper_post",
     *     path="/cheaper/{id}/",
     *     requirements={"id" = "\d+"}
     * )
     * @VIA\Response(status=200)
     */
    public function indexAction(int $id, Request $request)
    {

    }
}
