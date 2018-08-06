<?php

namespace CatalogBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use CatalogBundle\Bus\Categories\Query;
use CatalogBundle\Bus\Categories\Command;

/**
 * @VIA\Section("Категории каталога")
 */
class CategoriesController extends RestController
{
    /**
     * @VIA\Get(
     *     path="/rootCategories/forCompetitorsRevision/",
     *     description="Получить список основных категорий для  селекта",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="CatalogBundle\Bus\Categories\Query\GetRootFilterQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="CatalogBundle\Bus\Categories\Query\DTO\RootFilter")
     *     }
     * )
     */
    public function rootFilterAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetRootFilterQuery($request->query->all()), $index);

        return $index;
    }

    /**
     * @VIA\Get(
     *     path="/employees/forCompetitorsRevision/",
     *     description="Получить список добавивших сверку с конкурентом для селекта",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="CatalogBundle\Bus\Categories\Query\GetEmployeesFilterQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="CatalogBundle\Bus\Categories\Query\DTO\EmployeesFilter")
     *     }
     * )
     */
    public function employeesFilterAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetEmployeesFilterQuery($request->query->all()), $index);

        return $index;
    }
}