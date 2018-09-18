<?php 

namespace AppBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use AppBundle\Bus\Category\Query;

class CategoryController extends Controller
{
    /**
     * @VIA\Get(
     *     name="category_children", 
     *     path="/categories/{id}/",
     *     requirements={"id" = "\d+"},
     *     condition="request.isXmlHttpRequest()"
     * )
     */
    public function getAction(int $id, Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetChildrenQuery(['id' => $id]), $categories);

        if (0 === $id) {
            return $this->json([
                'html' => $this->renderView('Category/tree.html.twig', [
                    'categories' => $categories,
                ]),
            ]);
        }

        return $this->json([
            'categories' => $categories,
        ]);
    }
}
