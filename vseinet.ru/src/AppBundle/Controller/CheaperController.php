<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;

class CheaperController extends Controller
{
    /**
     * @VIA\Get(
     *     name="catalog_product_cheaper",
     *     path="/cheaper/{id}/",
     *     requirements={"id" = "\d+"},
     *     methods={"GET", "POST"},
     *     parameters={
     *         @VIA\Parameter(name="id", type="integer")
     *     }
     * )    
     */
    public function indexAction(int $id, Request $request)
    {

    }
}
