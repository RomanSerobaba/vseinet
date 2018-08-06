<?php

namespace DeliveryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use DeliveryBundle\Bus\Delivery\Query;
use DeliveryBundle\Bus\Delivery\Command;

/**
 * @VIA\Section("Доставка")
 */
class DeliveryController extends Controller
{
    /**
     * @VIA\Get(
     *     path="/deliveries/",
     *     description="Получить список доставок",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="DeliveryBundle\Bus\Delivery\Query\GetListQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="DeliveryBundle\Bus\Delivery\Query\DTO\Delivery", type="array")
     *     }
     * )
     */
    public function getListAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetListQuery($request->query->all()), $deliveries);

        return $deliveries;
    }    

    /**
     * @VIA\Put(
     *     path="/deliveries/{id}/",
     *     description="Изменить данные доставки",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="DeliveryBundle\Bus\Delivery\Command\UpdateCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
     public function updateAction(int $id, Request $request)
     {
         $this->get('command_bus')->handle(new Command\UpdateCommand($request->request->all(), ['id' => $id]));
     }

    /**
     * @VIA\Post(
     *     path="/deliveries/",
     *     description="Создать доставку",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="DeliveryBundle\Bus\Delivery\Command\CreateCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
     public function createAction(Request $request)
     {
        $uuid = $this->get('uuid.manager')->generate();
         $this->get('command_bus')->handle(new Command\CreateCommand($request->request->all(), ['uuid' => $uuid]));         

        return [
            'id' => $this->get('uuid.manager')->loadId($uuid),
        ];
     }

    /**
     * @VIA\Link(
     *     path="/deliveries/{id}/requests/",
     *     description="Добавить заявку в доставку",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="DeliveryBundle\Bus\Delivery\Command\LinkRequestCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
     public function linkRequestAction(int $id, Request $request)
     {
         $this->get('command_bus')->handle(new Command\LinkRequestCommand($request->query->all(), ['id' => $id]));
     }

    /**
     * @VIA\Unlink(
     *     path="/deliveries/{id}/requests/",
     *     description="Удалить заявку из доставки",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="DeliveryBundle\Bus\Delivery\Command\UnlinkRequestCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
     public function unlinkRequestAction(int $id, Request $request)
     {
         $this->get('command_bus')->handle(new Command\UnlinkRequestCommand($request->query->all(), ['id' => $id]));
     }
}
