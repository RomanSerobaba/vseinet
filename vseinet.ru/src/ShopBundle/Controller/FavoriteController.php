<?php

namespace ShopBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use ShopBundle\Bus\Favorite\Query;
use ShopBundle\Bus\Favorite\Command;

/**
 * @VIA\Section("Магазин:Избранное")
 */
class FavoriteController extends RestController
{
    /**
     * @VIA\Get(
     *     path="/favorite/",
     *     description="Получить список товаров",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ShopBundle\Bus\Favorite\Query\GetListQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="ShopBundle\Bus\Favorite\Query\DTO\Favorites", type="array")
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
     *     path="/favorite/{id}/",
     *     description="Удалить товар в избранном",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ShopBundle\Bus\Favorite\Command\DeleteCommand")
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
     *     path="/favorite/{id}/",
     *     description="Переместить в корзину",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ShopBundle\Bus\Favorite\Command\MoveCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function toggleAction(int $id)
    {
        $this->get('command_bus')->handle(new Command\MoveCommand(['id' => $id,]));
    }

    /**
     * @VIA\Post(
     *     path="/favorite/",
     *     description="Переместить в корзину",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ShopBundle\Bus\Favorite\Command\AddCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function addAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\AddCommand($request->request->all()));
    }
}
