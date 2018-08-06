<?php 

namespace ContentBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use ContentBundle\Bus\Template\Query;
use ContentBundle\Bus\Template\Command;

/**
 * @VIA\Section("Характеристики")
 * @deprecated
 */
class TemplateController extends RestController
{
    /**
     * @VIA\Get(
     *     path="/categories/forTemplates/",
     *     description="Получение списка категорий сайта",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Template\Query\GetCategoriesQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ContentBundle\Bus\Template\Query\DTO\Category")
     *     }
     * )
     */
    public function getCategoriesAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetCategoriesQuery($request->query->all()), $categories);

        return $categories;
    }    

    /**
     * @VIA\Get(
     *     path="/parserSources/forTemplates/",
     *     description="Получение списка источников парсера",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Template\Query\GetParserSourcesQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ContentBundle\Bus\Template\Query\DTO\ParserSource")
     *     }
     * )
     */
    public function getParserSourcesAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetParserSourcesQuery($request->query->all()), $sources);

        return $sources;
    }

    /**
     * @VIA\Get(
     *     path="/parserDetailGroups/forTemplates/",
     *     description="Получение списка групп характеристик парсера",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Template\Query\GetParserDetailGroupsQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ContentBundle\Bus\Template\Query\DTO\ParserDetailGroup")
     *     }
     * )
     */
    public function getParserDetailGroupsAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetParserDetailGroupsQuery($request->query->all()), $groups);

        return $groups;
    }

    /**
     * @VIA\Get(
     *     path="/parserDetails/forTemplates/",
     *     description="Получение списка характеристик парсера",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Template\Query\GetParserDetailsQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ContentBundle\Bus\Template\Query\DTO\ParserDetail")
     *     }
     * )
     */
    public function getParserDetailsAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetParserDetailsQuery($request->query->all()), $details);

        return $details;
    }
}