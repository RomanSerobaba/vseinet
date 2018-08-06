<?php

namespace ReservesBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use ReservesBundle\Bus\GoodsPackagingItem\Query;
use ReservesBundle\Bus\GoodsPackagingItem\Command;

/**
 * @VIA\Section("Комплектация/Разукомплектация")
 */
class GoodsPackagingItemController extends RestController
{

    /**
     * @VIA\Get(
     *     path="/goodsPackagings/{goodsPackagingId}/products/",
     *     description="Получить список товара комплектации/разкомплектации",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsPackagingItem\Query\GetQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ReservesBundle\Bus\GoodsPackagingItem\Query\DTO\GoodsPackaging")
     *     }
     * )
     */
    public function GetAction(int $goodsPackagingId)
    {
        $this->get('query_bus')->handle(new Query\GetQuery([
            'goodsPackagingId' => $goodsPackagingId
        ]), $items);

        return $items;
    }

    /**
     * @VIA\Post(
     *     path="/goodsPackagings/{goodsPackagingId}/products/{baseProductId}/",
     *     description="Добавить элемент спсика товаров в  документ комплектации/разкомплектации",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsPackagingItem\Command\CreateCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201
     * )
     */
    public function createAction(int $goodsPackagingId, int $baseProductId, Request $request)
    {
        $this->get('command_bus')->handle(new Command\CreateCommand($request->request->all(), [
            'goodsPackagingId' => $goodsPackagingId,
            'baseProductId' => $baseProductId
        ]));
    }

    /**
     * @VIA\Put(
     *     path="/goodsPackagings/{goodsPackagingId}/products/{baseProductId}/",
     *     description="Изменить элемент списка товаров в документе комплектации/разкомплектации",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsPackagingItem\Command\UpdateCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201
     * )
     */
    public function updateAction(int $goodsPackagingId, int $baseProductId, Request $request)
    {
        $this->get('command_bus')->handle(new Command\UpdateCommand($request->request->all(), [
            'goodsPackagingId' => $goodsPackagingId,
            'baseProductId' => $baseProductId
        ]));
    }

    /**
     * @VIA\Delete(
     *     path="/goodsPackagings/{goodsPackagingId}/products/{baseProductId}/",
     *     description="Удалить элемент списка товаров в документе комплектации/разкомплектации",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsPackagingItem\Command\DeleteCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function delAction(int $goodsPackagingId, int $baseProductId)
    {
        $this->get('command_bus')->handle(new Command\DeleteCommand([
            'goodsPackagingId' => $goodsPackagingId,
            'baseProductId' => $baseProductId
        ]));
    }


}
