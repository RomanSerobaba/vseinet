<?php 

namespace AdminBundle\Controller;

use AppBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Annotation as VIA;
use AdminBundle\Bus\Category\Query;

/**
 * @Security("is_granted('ROLE_EMPLOYEE')")
 */
class CategoryController extends Controller
{
    /**
     * @VIA\Get(
     *     name="admin_categories",
     *     path="/categories/",
     *     condition="request.isXmlHttpRequest()"
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="AdminBundle\Bus\Category\Query\DTO\Category")
     *     }
     * )
     */
    public function getAllAction()
    {
        $this->get('query_bus')->handle(new Query\GetAllQuery(), $categories);

        return $this->json([
            'categories' => $categories,
        ]);
    }

    /**
     * @VIA\Get(
     *     name="admin_search_categories",
     *     path="/categories/foundResult/",
     *     condition="request.isXmlHttpRequest()"
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="AdminBundle\Bus\Category\Query\DTO\CategoryFound")
     *     }
     * )
     */
    public function searchAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\SearchQuery($request->query->all()), $categories);

        return $this->json([
            'categories' => $categories,
        ]);
    }
}
