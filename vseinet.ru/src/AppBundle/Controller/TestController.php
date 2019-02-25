<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use AppBundle\Bus\Catalog\Query;

class TestController extends Controller
{
    /**
     * @VIA\Get(
     *     name="test_page",
     *     path="/test/{id}/",
     *     parameters={
     *         @VIA\Parameter(name="id", type="integer")
     *     }
     * )
     */
    public function testAction(int $id = 0, Request $request)
    {
        $this->get('catalog.product.finder.filter')->parse(['a' => 1, 'b' => 2]);
        $filter = $this->get('catalog.product.finder.filter');
        print_r($filter->availability);
        print_r($filter->brandIds);
        // return $this->render('Main/index.html.twig');
        exit;
    }
}
