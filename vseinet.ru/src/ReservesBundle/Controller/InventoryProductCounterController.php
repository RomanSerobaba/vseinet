<?php

namespace ReservesBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use ReservesBundle\Bus\InventoryProductCounter\Query;
use ReservesBundle\Bus\InventoryProductCounter\Command;

/**
 * @VIA\Section("Инвентаризация - подсчет товара")
 */
class InventoryProductCounterController extends RestController
{

    /**
     * @VIA\Get(
     *     path="/inventoriesCounters/",
     *     description="Получить список товара подсчитанного участником инвентаризации",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\InventoryProductCounter\Query\GetQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ReservesBundle\Bus\InventoryProductCounter\Query\DTO\InventoryParticipantCounter")
     *     }
     * )
     */
    public function GetAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetQuery($request->query->all()), $items);

        return $items;
    }

    /**
     * @VIA\Put(
     *     path="/inventoriesCounters/",
     *     description="Установить количество товара, подсчитанного участником",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\InventoryProductCounter\Command\SetCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201
     * )
     */
    public function setCountAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetCommand($request->request->all()));
    }

    /**
     * @VIA\Patch(
     *     path="/inventoriesCounters/",
     *     description="Прибавить количество товара, подсчитанного участником, на количество 'quantity'",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\InventoryProductCounter\Command\AddCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201
     * )
     */
    public function addCountAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\AddCommand($request->request->all()));
    }

    /**
     * @VIA\Put(
     *     path="/inventoriesCounters/comment/",
     *     description="Оставить комментарий к товару, подсчитанного участником",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\InventoryProductCounter\Command\CommentCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201
     * )
     */
    public function setCommentAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\CommentCommand($request->request->all()));
    }

    /**
     * @VIA\Delete(
     *     path="/inventoriesCounters/",
     *     description="Удалить итоги подсчета выбранного товара участника инвентаризации",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\InventoryProductCounter\Command\DeleteCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function delAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\DeleteCommand($request->request->all()));
    }

}
