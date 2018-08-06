<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use AppBundle\Bus\ApiMethodSection\Query;

/**
 * @VIA\Section("Права доступа")
 */
class ApiMethodSectionController extends RestController
{
    /**
     * @VIA\Get(
     *     path="/api/v1/section/list/",
     *     title="Получение списка разделов API",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="AppBundle\Bus\ApiMethodSection\Query\GetListQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="AppBundle\Entity\ApiMethodSection")
     *     }
     * )
     */
    public function getListAction()
    {
        $this->get('query_bus')->handle(new Query\GetListQuery(), $sections);

        return $sections;
    }

    /**
     * @VIA\Get(
     *     path="/api/v1/section/{id}/",
     *     title="Получение раздела API",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="AppBundle\Bus\ApiMethodSection\Query\GetQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="AppBundle\Entity\ApiMethodSection")
     *     }
     * )
     */
    public function getAction(int $id)
    {
        $this->get('query_bus')->handle(new Query\GetQuery(['id' => $id]), $section);

        return $section;
    }
}