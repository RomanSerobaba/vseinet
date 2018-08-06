<?php

namespace ReservesBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use ReservesBundle\Bus\GoodsAcceptance\Query;
use ReservesBundle\Bus\GoodsAcceptance\Command;

/**
 * @VIA\Section("Оприходование товара")
 */
class GoodsAcceptanceController extends RestController
{

    /**
     * @VIA\Get(
     *     path="/goodsAcceptances/",
     *     description="Получить список документов",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsAcceptance\Query\ListQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ReservesBundle\Bus\GoodsAcceptance\Query\DTO\DocumentList")
     *     }
     * )
     */
    public function GetAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\ListQuery($request->query->all()), $items);

        return $items;
    }

    /**
     * @VIA\Get(
     *     path="/goodsAcceptances/statuses/",
     *     description="Получить список статусов документа",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsAcceptance\Query\StatusesQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="\DocumentBundle\Prototipe\StatusesDTO")
     *     }
     * )
     */
    public function listStatusesAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\StatusesQuery($request->query->all()), $items);

        return $items;
    }

    /**
     * @VIA\Post(
     *     path="/goodsAcceptances/",
     *     description="Создать документ",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsAcceptance\Command\CreateCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201,
     *     properties={
     *         @VIA\Property(name="id", type="integer")
     *     }
     * )
     */
    public function createAction(Request $request)
    {
        $uuid = $this->get('uuid.manager')->generate();
        
        $this->get('command_bus')->handle(new Command\CreateCommand($request->request->all(), [
            'createdBy' => $this->getUser()->getId(), 
            'uuid' => $uuid]));

        return [
            'id' => $this->get('uuid.manager')->loadId($uuid)
        ];
    }

    /**
     * @VIA\Post(
     *     path="/goodsAcceptances/fromSupplies/",
     *     description="Создать документ на основании скписка закзаов поставщику",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsAcceptance\Command\CreateFromSupplyCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201,
     *     properties={
     *         @VIA\Property(name="id", type="integer")
     *     }
     * )
     */
    public function createFromSupplyAction(Request $request)
    {
        $uuid = $this->get('uuid.manager')->generate();
        
        $this->get('command_bus')->handle(new Command\CreateFromSupplyCommand($request->request->all(), [
            'createdBy' => $this->getUser()->getId(), 
            'uuid' => $uuid]));

        return [
            'id' => $this->get('uuid.manager')->loadId($uuid)
        ];
    }

    /**
     * @VIA\Get(
     *     path="/goodsAcceptances/{id}/",
     *     description="Получить шапку документа",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsAcceptance\Query\ItemQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="ReservesBundle\Bus\GoodsAcceptance\Query\DTO\Document")
     *     }
     * )
     */
    public function GetItemAction(int $id, Request $request)
    {
        $this->get('query_bus')->handle(new Query\ItemQuery($request->query->all(), ['id' => $id]), $item);
        return $item;
    }

    /**
     * @VIA\Put(
     *     path="/goodsAcceptances/{id}/",
     *     description="Изменить шапку документа",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsAcceptance\Command\UpdateCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201
     * )
     */
    public function updateAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\UpdateCommand($request->request->all(), [
            'id' => $id
        ]));
    }

    /**
     * @VIA\Delete(
     *     path="/goodsAcceptances/{id}/",
     *     description="Удалить документ",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsAcceptance\Command\DeleteCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function delAction(int $id)
    {
        $this->get('command_bus')->handle(new Command\DeleteCommand(['id' => $id]));
    }

    /**
     * @VIA\Put(
     *     path="/goodsAcceptances/{id}/statusCode/",
     *     description="Установить статус докмуента",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsAcceptance\Command\StatusCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201
     * )
     */
    public function setStatusAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\StatusCommand($request->request->all(), ['id' => $id]));
    }
    
}
