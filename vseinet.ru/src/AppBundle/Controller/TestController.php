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
    public function testAction(int $id, Request $request)
    {
        $image = $this->get('query_bus')->handle(new Query\GetCategoryImageQuery(['categoryId' => $id]));
        // return $this->render('Main/index.html.twig');
        var_dump($image);
        exit;
    }
}
