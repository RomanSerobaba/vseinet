<?php 

namespace ContentBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use ContentBundle\Bus\ManagerManagment\Query;
use AppBundle\Annotation as VIA;

/**
 * @deprecated
 * @VIA\Section("Контент-менеджеры")
 */
class ManagerManagmentController extends RestController
{
    /**
     * @VIA\Get(
     *      path="/contentManagers/",
     *      description="Получение структуры контент-менеджеров",
     *      parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ContentBundle\Bus\ManagerManagment\Query\GetStructureQuery")
     *      }
     * )
     * @VIA\Response(
     *      status=200,
     *      properties={
     *          @VIA\Property(model="ContentBundle\Bus\ManagerManagment\Query\DTO\Structure")
     *      }
     * )
     */
    public function getStructureAction()
    {
        $this->get('query_bus')->handle(new Query\GetStructureQuery(), $structure);

        return $structure;
    }

    /**
     * @VIA\Get(
     *     path="/contentManagers/foundResults/",
     *     description="Поиск сотрудников",
     *     parameters={
     *         @VIA\Parameter(model="ContentBundle\Bus\ManagerManagment\Query\SearchQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Parameter(type="array", model="ContentBundle\Bus\ManagerManagment\Query\DTO\Employee")
     *     }
     * )
     */
    public function searchAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\SearchQuery($request->query->all()), $employees);

        return $employees;
    }

    /**
     * @VIA\Get(
     *      path="/categories/forContentManagers/",
     *      description="Получение списка категорий",
     *      parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ContentBundle\Bus\ManagerManagment\Query\GetCategoriesQuery")
     *      }
     * )
     * @VIA\Response(
     *      status=200,
     *      properties={
     *          @VIA\Property(type="array", model="ContentBundle\Bus\ManagerManagment\Query\DTO\Category")
     *      }
     * )
     */
    public function getCategoriesAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetCategoriesQuery($request->query->all()), $categories);

        return $categories;
    }
}