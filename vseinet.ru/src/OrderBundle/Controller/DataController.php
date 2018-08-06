<?php

namespace OrderBundle\Controller;

use AppBundle\Controller\RestController;
use AppBundle\Annotation as VIA;
use OrderBundle\Bus\Data\Query;
use OrderBundle\Bus\Data\Command;
use Symfony\Component\HttpFoundation\Request;

/**
 * @VIA\Section("Данные по заказам")
 */
class DataController extends RestController
{
    /**
     * @VIA\Post(
     *     path="/orderItemsAnnuls/",
     *     description="Аннулирование позиции заказа",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="OrderBundle\Bus\Data\Command\AnnullCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function annulOrderItemAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\AnnullCommand($request->request->all()));
    }

    /**
     * @VIA\Post(
     *     path="/orders/forSupplies/",
     *     description="Создание заявки на лишний товар",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrderBundle\Bus\Data\Command\CreateResupplyOrderFromInvoiceCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201,
     *     properties={
     *         @VIA\Property(name="id", type="integer")
     *     }
     * )
     */
    public function createFromSupplierInvoiceAction(Request $request)
    {
        $uuid = $this->get('uuid.manager')->generate();
        $this->get('command_bus')->handle(new Command\CreateResupplyOrderFromInvoiceCommand($request->request->all(), ['uuid' => $uuid,]));

        return [
            'id' => $this->get('uuid.manager')->loadId($uuid),
        ];
    }

    /**
     * @VIA\Post(
     *     path="/orders/forLowCostPurchases/",
     *     description="Создать заказ товаров",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrderBundle\Bus\Data\Command\CreateResupplyOrderFromLowCostPurchasesCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201,
     *     properties={
     *         @VIA\Property(name="id", type="integer")
     *     }
     * )
     */
    public function createFromLowCostPurchasesAction(Request $request)
    {
        $uuid = $this->get('uuid.manager')->generate();
        $this->get('command_bus')->handle(new Command\CreateResupplyOrderFromLowCostPurchasesCommand($request->request->all(), ['uuid' => $uuid,]));

        return [
            'id' => $this->get('uuid.manager')->loadId($uuid),
        ];
    }

    /**
     * @VIA\Post(
     *     path="/orders/{id}/comments/",
     *     description="Сохранение комментария к заказу",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrderBundle\Bus\Data\Command\AddCommentCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201,
     *     properties={
     *         @VIA\Property(name="id", type="integer")
     *     }
     * )
     */
    public function addCommentAction(int $id, Request $request)
    {
        $uuid = $this->get('uuid.manager')->generate();
        $this->get('command_bus')->handle(new Command\AddCommentCommand($request->request->all(), ['id' => $id, 'uuid' => $uuid,]));

        return [
            'id' => $this->get('uuid.manager')->loadId($uuid),
        ];
    }

    /**
     * @VIA\Get(
     *     path="/orderAnnulCauses/",
     *     description="Получение списка причин аннулирования",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="OrderBundle\Bus\Data\Query\GetAnnulCausesQuery"),
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(name="comments", type="array", model="OrderBundle\Bus\Data\Query\DTO\AnnulCause")
     *     }
     * )
     */
    public function getAnnulCausesAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetAnnulCausesQuery($request->query->all()), $causes);

        return $causes;
    }

    /**
     * @VIA\Get(
     *     path="/orders/{id}/comments/",
     *     description="Показать комментарии к заказу",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="OrderBundle\Bus\Data\Query\GetOrderCommentsQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="OrderBundle\Bus\Item\Query\DTO\GetComments")
     *     }
     * )
     */
    public function getOrderCommentsAction(int $id)
    {
        $this->get('query_bus')->handle(new Query\GetOrderCommentsQuery(['id' => $id,]), $comments);

        return $comments;
    }

    /**
     * @VIA\Get(
     *     path="/orders/",
     *     description="Получение списка заказов по фильтру",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="OrderBundle\Bus\Data\Query\GetByFilterQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="OrderBundle\Bus\Data\Query\DTO\Items")
     *     }
     * )
     */
    public function getByFilterAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetByFilterQuery($request->query->all()), $orders);

        return $orders;
    }
}