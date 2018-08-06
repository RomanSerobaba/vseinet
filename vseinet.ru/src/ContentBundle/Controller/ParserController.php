<?php

namespace ContentBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use ContentBundle\Bus\Parser\Query;
use AppBundle\Annotation as VIA;

/**
 * @VIA\Section("Парсеры")
 */
class ParserController extends RestController
{
    /**
     * @VIA\Get(
     *      path="/parserTemplates/",
     *      description="Получение шаблона характеристик из парсеров",
     *      parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ContentBundle\Bus\Parser\Query\GetTemplateQuery")
     *      }
     * )
     * @VIA\Response(
     *      status=200,
     *      properties={
     *          @VIA\Property(model="ContentBundle\Bus\Parser\Query\DTO\Template")
     *      }
     * )
     */
    public function getTemplateAction(Request $request) 
    {
        $this->get('query_bus')->handle(new Query\GetTemplateQuery($request->query->all()), $template);

        return $template;
    }
}