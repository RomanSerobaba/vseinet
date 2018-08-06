<?php

namespace ReservesBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use ReservesBundle\Bus\GoodsAcceptanceItemCollapsed\Query;
use ReservesBundle\Bus\GoodsAcceptanceItemCollapsed\Command;

/**
 * @VIA\Section("Оприходование товара - работа со свернутым списком товаров")
 */
class GoodsAcceptanceItemCollapsedController extends RestController
{


    /**
     * @VIA\Get(
     *     path="/goodsAcceptances/deltaResult/{uuid}/",
     *     description="Свёрнутый список. Запросить направления для сортировки товара",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsAcceptanceItemCollapsed\Query\GetDeltaResultQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ReservesBundle\Bus\GoodsAcceptanceItemCollapsed\Query\DTO\DeltaResultDTO")
     *     }
     * )
     */
    public function deltaResultAction($uuid, Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetDeltaResultQuery($request->query->all(), ['uuid' => $uuid]), $result);
        
        return $result;
    }

    /**
     * @VIA\Get(
     *     path="/goodsAcceptances/{goodsAcceptanceId}/collapsedProducts/",
     *     description="Свернутый список. Получить список элементов документа",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsAcceptanceItemCollapsed\Query\GetQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ReservesBundle\Bus\GoodsAcceptanceItemCollapsed\Query\DTO\GoodsAcceptanceItem")
     *     }
     * )
     */
    public function GetAction(int $goodsAcceptanceId, Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetQuery($request->query->all(), ['goodsAcceptanceId' => $goodsAcceptanceId]), $items);
        return $items;
    }

    /**
     * @VIA\Post(
     *     path="/goodsAcceptances/{goodsAcceptanceId}/collapsedProducts/",
     *     description="Свернутый список. Добавить строку товара.",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsAcceptanceItemCollapsed\Command\AddCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201
     * )
     */
    public function addAction(int $goodsAcceptanceId, Request $request)
    {
        $this->get('command_bus')->handle(new Command\AddCommand($request->request->all(),
                ['goodsAcceptanceId' => $goodsAcceptanceId]));
    }

    /**
     * @VIA\Put(
     *     path="/goodsAcceptances/{goodsAcceptanceId}/collapsedProducts/",
     *     description="Свёрнутый список. Установить количество отгруженного товара",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsAcceptanceItemCollapsed\Command\SetCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=202,
     *     properties={
     *         @VIA\Property(name="uuid", type="string")
     *     }
     * )
     */
    public function setAction(int $goodsAcceptanceId, Request $request)
    {
        $uuid = $this->get('uuid.manager')->generate();
        
        $this->get('command_bus')->handle(new Command\SetCommand($request->request->all(),[
                'goodsAcceptanceId' => $goodsAcceptanceId,
                'uuid' => $uuid]));
        
        return ['uuid' => $uuid];
    }

    /**
     * @VIA\Patch(
     *     path="/goodsAcceptances/{goodsAcceptanceId}/collapsedProducts/",
     *     description="Свёрнутый список. Увеличить/уменьшить количество отгруженного товара",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsAcceptanceItemCollapsed\Command\DeltaCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=202,
     *     properties={
     *         @VIA\Property(name="uuid", type="string")
     *     }
     * )
     */
    public function deltaAction(int $goodsAcceptanceId, Request $request)
    {
        $uuid = $this->get('uuid.manager')->generate();
        
        $this->get('command_bus')->handle(new Command\DeltaCommand($request->request->all(), [
            'goodsAcceptanceId' => $goodsAcceptanceId,
            'uuid' => $uuid]));
        
        return ['uuid' => $uuid];
    }

    /**
     * @VIA\Delete(
     *     path="/goodsAcceptances/{goodsAcceptanceId}/collapsedProducts/",
     *     title="Свернутый список. Удаление строки документа",
     *     description="
     * Отмена ранее сделанной операции по её UUID.
     * В некоторых случаях отмена невозможна, тогда возникает ошибка.",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsAcceptanceItemCollapsed\Command\DeleteCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function deleteAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\DeleteCommand($request->query->all()));
    }

}
