<?php

namespace ShopBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use ShopBundle\Bus\Banner\Query;
use ShopBundle\Bus\Banner\Command;

/**
 * @VIA\Section("Магазин:Баннеры")
 */
class BannerController extends RestController
{
    /**
     * @VIA\Get(
     *     path="/promotion/banner/",
     *     description="Список банеров",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ShopBundle\Bus\Banner\Query\GetListQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="ShopBundle\Bus\Banner\Query\DTO\Banners", type="array")
     *     }
     * )
     */
    public function getListAction()
    {
        $this->get('query_bus')->handle(new Query\GetListQuery(), $list);

        return $list;
    }

    /**
     * @VIA\Get(
     *     path="/promotion/banner/site/",
     *     description="Список банеров для главной",
     *     parameters={
     *         @VIA\Parameter(model="ShopBundle\Bus\Banner\Query\GetSiteListQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="ShopBundle\Bus\Banner\Query\DTO\Banner", type="array")
     *     }
     * )
     */
    public function getSiteListAction()
    {
        $this->get('query_bus')->handle(new Query\GetSiteListQuery(), $list);

        return $list;
    }

    /**
     * @VIA\Get(
     *     path="/promotion/banner/{id}/",
     *     description="Данные баннера",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ShopBundle\Bus\Banner\Query\GetBannerQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="ShopBundle\Bus\Banner\Query\DTO\Banner", type="object")
     *     }
     * )
     */
    public function getBannerAction(int $id)
    {
        $this->get('query_bus')->handle(new Query\GetBannerQuery(['id' => $id,]), $banner);

        return $banner;
    }

    /**
     * @VIA\Patch(
     *     path="/promotion/banner/{id}/tabfix/",
     *     description="Зафиксировать таб",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ShopBundle\Bus\Banner\Command\TabFixCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function tabFixAction(int $id)
    {
        $this->get('command_bus')->handle(new Command\TabFixCommand(['id' => $id,]));
    }

    /**
     * @VIA\Patch(
     *     path="/promotion/banner/{id}/toggle/",
     *     description="Включить/отключить баннер",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ShopBundle\Bus\Banner\Command\ToggleCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function toggleAction(int $id)
    {
        $this->get('command_bus')->handle(new Command\ToggleCommand(['id' => $id,]));
    }

    /**
     * @VIA\Patch(
     *     path="/promotion/banner/{id}/weight/",
     *     description="Изменить вес",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ShopBundle\Bus\Banner\Command\WeightCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function weightAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\WeightCommand($request->request->all(), ['id' => $id,]));
    }

    /**
     * @VIA\Delete(
     *     path="/promotion/banner/{id}/",
     *     description="Удалить баннер",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ShopBundle\Bus\Banner\Command\DeleteCommand")
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
     * @VIA\Post(
     *     path="/promotion/banner/",
     *     description="Создать баннер",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ShopBundle\Bus\Banner\Command\CreateCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function createAction(Request $request)
    {
        $uuid = $this->get('uuid.manager')->generate();
        $this->get('command_bus')->handle(new Command\CreateCommand($request->request->all(), ['uuid' => $uuid,]));

        return [
            'id' => $this->get('uuid.manager')->loadId($uuid),
        ];
    }

    /**
     * @VIA\Put(
     *     path="/promotion/banner/{id}/",
     *     description="Редактировать баннер",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ShopBundle\Bus\Banner\Command\EditCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function editAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\EditCommand($request->request->all(), ['id' => $id,]));
        
        $this->get('query_bus')->handle(new Query\GetBannerQuery(['id' => $id,]), $banner);
        
        foreach ($banner->productList as $product) {
            $this->get('command_bus')->handle(new Command\DeleteProductCommand(['id' => $product->id,]));
        }
        
        foreach ($request->request->get('productList', []) as $product) {
            $this->get('command_bus')->handle(new Command\AddProductCommand($product, ['bannerId' => $id,]));
        }
    }
}
