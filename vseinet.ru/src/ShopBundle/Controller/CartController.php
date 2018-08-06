<?php

namespace ShopBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use ShopBundle\Bus\Cart\Query;
use ShopBundle\Bus\Cart\Command;

/**
 * @VIA\Section("Магазин:Корзина")
 */
class CartController extends RestController
{
    /**
     * @VIA\Get(
     *     path="/cart/",
     *     description="Получить список товаров",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ShopBundle\Bus\Cart\Query\GetListQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="ShopBundle\Bus\Cart\Query\DTO\Cart", type="array")
     *     }
     * )
     */
    public function getListAction()
    {
        $this->get('query_bus')->handle(new Query\GetListQuery(), $list);

        return $list;
    }

    /**
     * @VIA\Delete(
     *     path="/cart/",
     *     description="Очистить корзину",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ShopBundle\Bus\Cart\Command\ClearCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function clearAction()
    {
        $this->get('command_bus')->handle(new Command\ClearCommand());
    }

    /**
     * @VIA\Patch(
     *     path="/cart/{id}/quantity/",
     *     description="Изменить количество товара в корзине",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ShopBundle\Bus\Cart\Command\QuantityCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function quantityAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\QuantityCommand($request->request->all(), ['id' => $id,]));
    }

    /**
     * @VIA\Delete(
     *     path="/cart/{id}/",
     *     description="Удалить товар в корзине",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ShopBundle\Bus\Cart\Command\DeleteCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function deleteAction(int $id)
    {
        $this->get('command_bus')->handle(new Command\DeleteCommand(['id' => $id,]));
    }

    /**
     * @VIA\Put(
     *     path="/cart/{id}/favorite/",
     *     description="Пемереместить в избранное",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ShopBundle\Bus\Cart\Command\FavoriteCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function favoriteAction(int $id)
    {
        $this->get('command_bus')->handle(new Command\FavoriteCommand(['id' => $id,]));
    }

    /**
     * @VIA\Post(
     *     path="/cart/",
     *     description="Применить код для получения скидки",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ShopBundle\Bus\Cart\Command\DiscountCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="ShopBundle\Bus\Cart\Query\DTO\Cart", type="array")
     *     }
     * )
     */
    public function discountAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\DiscountCommand($request->request->all()));

        $this->get('query_bus')->handle(new Query\GetListQuery(), $list);

        return $list;
    }
}
