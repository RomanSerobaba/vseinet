<?php 

namespace ContentBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use ContentBundle\Bus\ColorComposite\Query;
use ContentBundle\Bus\ColorComposite\Command;

/**
 * @VIA\Section("Цвета")
 */
class ColorCompositeController extends RestController
{    
    /**
     * @VIA\Get(
     *     path="/colorCompositeSchemas/",
     *     description="Получение схем форматирования составных цветов",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ContentBundle\Bus\ColorComposite\Query\GetSchemasQuery")   
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ContentBundle\Bus\ColorComposite\Query\DTO\Schema")
     *     }
     * )
     */
    public function getSchemasAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetSchemasQuery($request->query->all()), $schemas);

        return $schemas;
    }

    /**
     * @VIA\Get(
     *     path="/colorComposites/formedValue/",
     *     description="Получение форматированного значения составного цвета",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ContentBundle\Bus\ColorComposite\Query\GetFormedValueQuery")   
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(name="formedValue", type="string")
     *     }
     * )
     */
    public function getFormedValueAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetFormedValueQuery($request->query->all()), $formedValue);

        return [
            'formedValue' => $formedValue,
        ];
    }

    /**
     * @VIA\Get(
     *     path="/colorComposites/{id}/",
     *     requirements={"id"="\d+"},
     *     description="Получение составного цвета",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ContentBundle\Bus\ColorComposite\Query\GetQuery")   
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="ContentBundle\Entity\ColorComposite")
     *     }
     * )
     */
    public function getAction(int $id)
    {
        $this->get('query_bus')->handle(new Query\GetQuery(['id' => $id]), $composite);

        return $composite;
    }
}