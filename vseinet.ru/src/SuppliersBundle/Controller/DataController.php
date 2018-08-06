<?php

namespace SuppliersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use SuppliersBundle\Bus\Data\Query;
use SuppliersBundle\Bus\Data\Command;

/**
 * @VIA\Description("Данные поставщиков")
 * @VIA\Section("Данные поставщиков")
 */
class DataController extends Controller
{

    /**
     * @VIA\Get(
     *     path="/suppliers/forGoodsAcceptance/",
     *     description="Получить список поставщиков с уникальными идентификаторами не оформленных к приёмке счетов",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="SuppliersBundle\Bus\Data\Query\ListForGoodsAcceptanceQuerry")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *          @VIA\Parameter(type="array", model="SuppliersBundle\Bus\Data\Query\DTO\ListForGoodsAcceptanceDTO")
     *     }
     * )
     */
    public function listForGoodsAcceptanceAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\ListForGoodsAcceptanceQuerry($request->query->all()), $items);
        return $items;
    }

    /**
     * @VIA\Put(
     *     path="/suppliers/{id}/shippingInfo/",
     *     description="Изменить крайний срок заказа и ближайшую дату доставки ",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="SuppliersBundle\Bus\Data\Command\SetShippingInfoCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function addItemsAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetShippingInfoCommand($request->request->all(), ['id' => $id,]));
    }
}
