<?php

namespace ReservesBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use ReservesBundle\Bus\GoodsIssueDocType\Query;
use ReservesBundle\Bus\GoodsIssueDocType\Command;

/**
 * @VIA\Section("Претензии - Список типов претензий")
 */
class GoodsIssueDocTypeController extends RestController
{

    /**
     * @VIA\Get(
     *     path="/goodsIssueDocType/",
     *     description="Получить список типов претензий",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsIssueDocType\Query\GetListQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ReservesBundle\Bus\GoodsIssueDocType\Query\DTO\GoodsIssueDocType")
     *     }
     * )
     */
    public function GetAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetLIstQuery($request->query->all()), $items);

        return $items;
    }

    /**
     * @VIA\Post(
     *     path="/goodsIssueDocType/",
     *     description="Создать тип претензии",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsIssueDocType\Command\CreateCommand")
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
     * @VIA\Get(
     *     path="/goodsIssueDocType/{id}/",
     *     description="Получить тип претензии",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsIssueDocType\Query\GetItemQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="ReservesBundle\Entity\GoodsIssueDocType")
     *     }
     * )
     */
    public function GetItemAction(int $id)
    {
        $this->get('query_bus')->handle(new Query\GetItemQuery(['id' => $id]), $item);
        return $item;
    }

    /**
     * @VIA\Put(
     *     path="/goodsIssueDocType/{id}/",
     *     description="Изменить тип претензии",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsIssueDocType\Command\UpdateCommand")
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
     *     path="/goodsIssueDocType/{id}/",
     *     description="Удалить тип претензии",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ReservesBundle\Bus\GoodsIssueDocType\Command\DeleteCommand")
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


}
