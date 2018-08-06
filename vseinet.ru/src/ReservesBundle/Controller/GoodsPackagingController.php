<?php

namespace ReservesBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use ReservesBundle\Bus\GoodsPackaging\Query;
use ReservesBundle\Bus\GoodsPackaging\Command;

/**
 * @VIA\Section("Комплектация/Разукомплектация")
 */
class GoodsPackagingController extends RestController
{

    /**
     * @VIA\Get(
     *     path="/goodsPackagings/",
     *     description="Получить список",
      parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsPackaging\Query\ListQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", name="items", model="ReservesBundle\Bus\GoodsPackaging\Query\DTO\GoodsPackaging"),
     *         @VIA\Property(type="integer", name="total")
     *     }
     * )
     */
    public function listAction(Request $request)
    {

        $this->get('query_bus')->handle(new Query\ListQuery($request->query->all()), $items);

        return $items;

    }

    /**
     * @VIA\Post(
     *     path="/goodsPackagings/",
     *     description="Создать документ",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsPackaging\Command\CreateCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201,
     *     properties={
     *         @VIA\Property(name="id", type="integer")
     *     }
     * )
     */    public function createAction(Request $request)
    {

        $document = $this->get('document.GoodsPackaging');
        return ['id' => $document
                ->create(new Command\CreateCommand($request->request->all(), [
                    'createdBy' => $this->getUser()->getId()]))];

    }

    /**
     * @VIA\Get(
     *     path="/goodsPackagings/{id}/",
     *     description="Получить шапку",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsPackaging\Query\ItemQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="ReservesBundle\Bus\GoodsPackaging\Query\DTO\GoodsPackagingItem")
     *     }
     * )
     */
    public function getAction(int $id)
    {
        $this->get('query_bus')->handle(new Query\ItemQuery(['id' => $id]), $item);
        return $item;
    }

    /**
     * @VIA\Put(
     *     path="/goodsPackagings/{id}/",
     *     description="Изменить документ",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsPackaging\Command\UpdateCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201
     * )
     */
    public function setAction(int $id, Request $request)
    {
        $document = $this->get('document.GoodsPackaging');
        $document->update(new Command\UpdateCommand($request->request->all(), [
            'id' => $id
        ]));
    }

    /**
     * @VIA\Delete(
     *     path="/goodsPackagings/{id}/",
     *     description="Удалить документ",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsPackaging\Command\DeleteCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function delAction(int $id)
    {
        $document = $this->get('document.GoodsPackaging');
        $document->delete($id);        
    }

    /**
     * @VIA\Put(
     *     path="/goodsPackagings/{id}/isCompleted/",
     *     description="Закрытие (завершение)/открытие документа для редактирования.",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsPackaging\Command\CompletedCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201
     * )
     */
    public function completedAction(int $id, Request $request)
    {
        $document = $this->get('document.GoodsPackaging');
        $document->setCompleted($id, $request->get('completed'));
    }

}
