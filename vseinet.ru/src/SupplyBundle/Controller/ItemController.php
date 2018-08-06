<?php

namespace SupplyBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use SupplyBundle\Bus\Item\Query;

/**
 * @VIA\Section("Товар накладной")
 */
class ItemController extends RestController
{

    /**
     * @VIA\Get(
     *     path="/supplies/{id}/items/for1C/",
     *     description="Получение товаров накладной ",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Item\Query\GetFor1CQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array")
     *     }
     * )
     */
    public function getFor1CAction(int $id, Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetFor1CQuery($request->query->all(), ['id' => $id,]), $list);

        return $list;
    }
}