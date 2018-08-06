<?php

namespace SupplyBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use SupplyBundle\Bus\Orders\Query;
use SupplyBundle\Bus\Orders\Command;
use SiteBundle\Bus\Cart;

/**
 * @VIA\Description("Заказы")
 * @VIA\Section("Заказы")
 */
class OrdersController extends RestController
{
    /**
     * @VIA\Patch(
     *     path="/orders/{id}/markReached/",
     *     description="Пометка недозвона",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Orders\Command\MarkReachedCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function markReachedAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\MarkReachedCommand($request->request->all(), ['id' => $id,]));
    }

    /**
     * @VIA\Patch(
     *     path="/orders/addToCart/",
     *     description="Добавление в корзину",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Orders\Command\AddToCartCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function addToCartAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\AddToCartCommand($request->request->all()));
        $this->get('command_bus')->handle(new Cart\Command\AddCommand(['baseProductId' => $request->request->get('baseProductId'), 'quantity' => 1,]));
    }

    /**
     * @VIA\Patch(
     *     path="/orders/cancelPosition/",
     *     description="Аннулировать позицию",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Orders\Command\CancelPositionCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function cancelPositionAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\CancelPositionCommand($request->request->all()));
    }

    /**
     * @VIA\Post(
     *     path="/orders/{id}/addComment/",
     *     description="Написание комментария",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="SupplyBundle\Bus\Orders\Command\AddCommentCommand")
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
        $this->get('command_bus')->handle(new Command\AddCommentCommand(['id' => $id,], $request->request->all()), ['uuid' => $uuid,]);

        return [
            'id' => $this->get('uuid.manager')->loadId($uuid),
        ];
    }

    /**
     * @VIA\Post(
     *     path="/orders/addGoodsIssue/",
     *     description="Создание претензии",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="SupplyBundle\Bus\Orders\Command\AddGoodsIssueCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201,
     *     properties={
     *         @VIA\Property(name="id", type="integer")
     *     }
     * )
     */
    public function addGoodsIssueAction(Request $request)
    {
        $uuid = $this->get('uuid.manager')->generate();
        $this->get('command_bus')->handle(new Command\AddGoodsIssueCommand($request->request->all()), ['uuid' => $uuid,]);

        return [
            'id' => $this->get('uuid.manager')->loadId($uuid),
        ];
    }

    /**
     * @VIA\Post(
     *     path="/orders/{id}/addItem/",
     *     description="Добавление позиции в заказ",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="SupplyBundle\Bus\Orders\Command\AddOrderItemCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201,
     *     properties={
     *         @VIA\Property(name="id", type="integer")
     *     }
     * )
     */
    public function addItemAction(int $id, Request $request)
    {
        $uuid = $this->get('uuid.manager')->generate();
        $this->get('command_bus')->handle(new Command\AddOrderItemCommand(['id' => $id,'uuid' => $uuid,], $request->request->all()));

        return [
            'id' => $this->get('uuid.manager')->loadId($uuid),
        ];
    }

    /**
     * @VIA\Patch(
     *     path="/orders/updateRetailPrice/",
     *     description="Поменять цену продажи",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Orders\Command\UpdateRetailPriceCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function updateRetailPriceAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\UpdateRetailPriceCommand($request));
    }

    /**
     * @VIA\Patch(
     *     path="/orders/updateItemQuantity/",
     *     description="Изменение количества позиции заказа",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Orders\Command\UpdateItemQuantityCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function updateItemQuantityAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\UpdateItemQuantityCommand($request));
    }

    /**
     * @VIA\Patch(
     *     path="/orders/{id}/updatePaymentType/",
     *     description="Изменение основного способа оплаты заказа",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Orders\Command\UpdatePaymentTypeCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function updatePaymentTypeAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\UpdatePaymentTypeCommand($request));
    }

    /**
     * @VIA\Get(
     *     path="/orders/managers/",
     *     description="Получение списка менеджеров",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Orders\Query\GetManagersQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="SupplyBundle\Bus\Orders\Query\DTO\Managers")
     *     }
     * )
     */
    public function managersAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetManagersQuery($request->query->all()), $managers);

        return $managers;
    }

    /**
     * @VIA\Patch(
     *     path="/orders/reserve/",
     *     description="Резервирование товара под заказ (не реализовано)",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Orders\Command\UpdateReserveCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function reserveAction(Request $request)
    {
        $this->get('query_bus')->handle(new Command\UpdateReserveCommand($request));
    }

    /**
     * @VIA\Get(
     *     path="/orders/cities/",
     *     description="Получение списка городов",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Orders\Query\GetCitiesQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="SupplyBundle\Bus\Orders\Query\DTO\Cities")
     *     }
     * )
     */
    public function citiesAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetCitiesQuery($request->query->all()), $cities);

        return $cities;
    }

    /**
     * @VIA\Get(
     *     path="/orders/suppliers/",
     *     description="Получение списка поставщиков",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Orders\Query\GetSuppliersQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="SupplyBundle\Bus\Orders\Query\DTO\Suppliers")
     *     }
     * )
     */
    public function suppliersAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetSuppliersQuery($request->query->all()), $statuses);

        return $statuses;
    }

    /**
     * @VIA\Get(
     *     path="/orders/payments/",
     *     description="Получение списка способов оплаты",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Orders\Query\GetPaymentsQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="OrderBundle\Entity\PaymentType")
     *     }
     * )
     */
    public function paymentsAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetPaymentsQuery($request->query->all()), $prepayments);

        return $prepayments;
    }

    /**
     * @VIA\Get(
     *     path="/orders/delivery/",
     *     description="Получение списка способов доставки",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Orders\Query\GetDeliveryQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="OrderBundle\Entity\DeliveryType")
     *     }
     * )
     */
    public function deliveryAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetDeliveryQuery($request->query->all()), $prepayments);

        return $prepayments;
    }

    /**
     * @VIA\Get(
     *     path="/orders/{id}/smsLogs/",
     *     description="Показать логи смс-сообщений",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Orders\Query\GetSmsLogsQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array")
     *     }
     * )
     */
    public function getSmsLogsAction(int $id)
    {
        $this->get('query_bus')->handle(new Query\GetSmsLogsQuery(['id' => $id,]), $logs);

        return $logs;
    }
}