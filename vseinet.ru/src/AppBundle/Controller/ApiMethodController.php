<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use AppBundle\Bus\ApiMethod\Query;

/**
 * @VIA\Section("Права доступа")
 */
class ApiMethodController extends RestController
{   
    /**
     * @VIA\Get(
     *     path="/api/v1/method/list/",
     *     title="Получение списка методов API",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="AppBundle\Bus\ApiMethod\Query\GetListQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="AppBundle\Entity\ApiMethod")
     *     }
     * )
     */
    public function getListAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetListQuery($request->query->all()), $methods);

        return $methods;
    }

    /**
     * @VIA\Get(
     *     path="/api/v1/method/{id}/",
     *     title="Получение метода API",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="AppBundle\Bus\ApiMethod\Query\GetQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="AppBundle\Entity\ApiMethod")
     *     }
     * )
     */
    public function getAction(int $id)
    {
        $this->get('query_bus')->handle(new Query\GetQuery(['id' => $id]), $method);

        return $method;
    }
}