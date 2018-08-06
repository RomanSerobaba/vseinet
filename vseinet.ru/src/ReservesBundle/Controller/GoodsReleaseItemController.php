<?php

namespace ReservesBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use ReservesBundle\Bus\GoodsReleaseDocItem\Query;
use ReservesBundle\Bus\GoodsReleaseDocItem\Command;

/**
 * @VIA\Section("Выдача товара клиенту - список товара")
 */
class GoodsReleaseItemController extends RestController
{

    /////////////////////////////////////////////////////////////////////////
    //
    //  Работа с детальным списком
    //
    
    /**
     * @VIA\Get(
     *     path="/goodsReleases/{goodsReleaseId}/products/",
     *     description="Получить список товара для выдачи клиенту",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsReleaseDocItem\Query\GetQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ReservesBundle\Bus\GoodsReleaseDocItem\Query\DTO\GoodsReleaseDocItem")
     *     }
     * )
     */
    public function getAction(int $goodsReleaseId)
    {
        $this->get('query_bus')->handle(new Query\GetQuery([
            'goodsReleaseId' => $goodsReleaseId
        ]), $items);

        return $items;
    }


    /**
     * @VIA\Get(
     *     path="/goodsReleases/{goodsReleaseId}/product/{id}/",
     *     description="Получить элемент списка товара документа по идентификатору позиции (строки)",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsReleaseDocItem\Query\GetItemQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="ReservesBundle\Bus\GoodsReleaseDocItem\Query\DTO\GoodsReleaseDocItem")
     *     }
     * )
     */
    public function getItemAction(int $goodsReleaseId, int $id, Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetItemQuery($request->request->all(), [
            'goodsReleaseId' => $goodsReleaseId,
            'id' => $id
        ]), $item);
        return $item;
    }

    /**
     * @VIA\Post(
     *     path="/goodsReleases/{goodsReleaseId}/product/",
     *     description="Добавить элемент спсика товаров в документ",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsReleaseDocItem\Command\CreateCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201,
     *     properties={
     *         @VIA\Property(model="ReservesBundle\Bus\GoodsReleaseDoc\Query\DTO\Document")
     *     }
     * )
     */
    public function createAction(int $goodsReleaseId, Request $request)
    {
        $uuid = $this->get('uuid.manager')->generate();
        
        $this->get('command_bus')->handle(new Command\CreateCommand($request->request->all(), [
            'goodsReleaseId' => $goodsReleaseId,
            'uuid' => $uuid,
        ]));

        return [
            'id' => $this->get('uuid.manager')->loadId($uuid)
        ];
    }

    /**
     * @VIA\Put(
     *     path="/goodsReleases/{goodsReleaseId}/product/{id}/",
     *     description="Изменить элемент списка товаров в документе",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsReleaseDocItem\Command\UpdateCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201
     * )
     */
    public function updateAction(int $goodsReleaseId, int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\UpdateCommand($request->request->all(), [
            'goodsReleaseId' => $goodsReleaseId,
            'id' => $id,
        ]));
    }

    /**
     * @VIA\Post(
     *     path="/goodsReleases/{goodsReleaseId}/product/{baseProductId}/delta/{deltaQuantity}/",
     *     description="Изменить элемент списка товаров в документе",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsReleaseDocItem\Command\DeltaCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201
     * )
     */
    public function deltaAction(int $goodsReleaseId, int $baseProductId,int $deltaQuantity, Request $request)
    {
        $this->get('command_bus')->handle(new Command\DeltaCommand($request->request->all(), [
            'goodsReleaseId' => $goodsReleaseId,
            'baseProductId' => $baseProductId,
            'deltaQuantity' => $deltaQuantity,
        ]));
    }

    /////////////////////////////////////////////////////////////////////////
    //
    //  Работа со свёрнутым списком
    //
    
    /**
     * @VIA\Get(
     *     path="/goodsReleases/{goodsReleaseId}/collapsedProducts/",
     *     title="Получить свернутый список продуктов документа",
     *     description="*Свернутый* интерфейс документа. Спсиок товара отдаётся без заказов и партий.",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsReleaseDocItem\Query\GetCollapsedListQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ReservesBundle\Bus\GoodsReleaseDocItem\Query\DTO\CollapsedGoodsReleaseDocItem")
     *     }
     * )
     */
    public function collapsedGetAction(int $goodsReleaseId)
    {
        $this->get('query_bus')
                ->handle(new Query\GetCollapsedListQuery(
                        ['goodsReleaseId' => $goodsReleaseId]), $items);

        return $items;
    }
    
    /**
     * @VIA\Patch(
     *     path="/goodsReleases/{goodsReleaseId}/collapsedProducts/",
     *     title="Отгрузка/возврат элемента свёрнутого списка продуктов",
     *     description="*Свернутый* интерфейс документа. Реализует отгрузку продуктов/палет, отмену отгрузки продуктов/палет, пометку/отмену пометки проблемных продуктов.",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsReleaseDocItem\Command\DeltaCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201
     * )
     */
    public function collapsedDeltaAction(int $goodsReleaseId, Request $request)
    {
        
        $this->get('command_bus')
                ->handle(new Command\DeltaCommand($request->request->all(),
                        ['goodsReleaseId' => $goodsReleaseId]));

        return;
    }

    /**
     * @VIA\Put(
     *     path="/goodsReleases/{goodsReleaseId}/collapsedProducts/",
     *     title="Изменение отгруженного количества в свёрнутом списке продуктов/палет",
     *     description="*Свернутый* интерфейс документа. Реализует изменение отгруженного количества продуктов/палет.",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsReleaseDocItem\Command\SetQuantityCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201
     * )
     */
    public function collapsedSetQuantityAction(int $goodsReleaseId, Request $request)
    {
        $uuid = $this->get('uuid.manager')->generate();
        
        $this->get('command_bus')->handle(
                new Command\SetQuantityCommand($request->request->all(), [
                    'goodsReleaseId' => $goodsReleaseId]));

        return;
    }
    
}
