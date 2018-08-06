<?php

namespace ServiceBundle\Services;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\DocumentTypeCode;
use AppBundle\Enum\GoodsReserveOperationCode;
use AppBundle\Enum\GoodsReserveType;
use AppBundle\Enum\SupplierReserve;
use AppBundle\Enum\SupplierReserveStatus;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use ReservesBundle\Entity\GoodsRequest;
use OrderBundle\Entity\OrderItem;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GoodsRequestService extends MessageHandler
{
    // Сброс резерва
    public function resetReserve(int $goodsRequestId) : void
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $orderItem = $em->getRepository(OrderItem::class)->find($goodsRequestId);
        if (!$orderItem instanceof OrderItem) {
            throw new NotFoundHttpException(sprintf('Позиция заказа %u не найдена', $goodsRequestId));
        }

        $query = $em->createQuery('
            SELECT
                supply_item_id,
                geo_room_id,
                shipment_id,
                reserve_type,
                SUM(delta) AS quantity 
            FROM
                goods_reserve_log 
            WHERE
                goods_request_id = :goods_request_id 
            GROUP BY
                shipment_id,
                geo_room_id,
                supply_item_id,
                reserve_type 
            HAVING
                SUM(delta) > 0
        ');
        $query->setParameter('goods_request_id', $goodsRequestId);

        $items = $query->getArrayResult();
        $reserveService = $this->get('service.reserve');

        foreach ($items as $item) {
            $reserveService->change(
                $orderItem->getBaseProductId(),
                $item['supply_item_id'],
                DocumentTypeCode::GOODS_REQUEST,
                $goodsRequestId,
                GoodsReserveOperationCode::ANNUL_RESERVE,
                $item['reserve_type'],
                $item['geo_room_id'],
                -$item['quantity'],
                null,
                $goodsRequestId,
                null,null,null,
                $item['shipment_id']
            );

            $reserveService->change(
                $orderItem->getBaseProductId(),
                $item['supply_item_id'],
                DocumentTypeCode::ORDER_ITEM,
                $goodsRequestId,
                GoodsReserveOperationCode::ANNUL_RESERVE,
                GoodsReserveType::NEW,
                $item['geo_room_id'],
                $item['quantity'],
                null,
                null,
                null,null,null,
                $item['shipment_id']
            );
        }
    }

    //Аннулирование запроса
    public function annul(int $goodsRequestId) : void
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery('
            SELECT
                id,
                equipment_id,
                goods_issue_id 
            FROM
                goods_request 
            WHERE
                is_annulled = FALSE 
                AND is_completed = FALSE 
                AND id = :goods_request_id
        ');
        $query->setParameter('goods_request_id', $goodsRequestId);

        $item = $query->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

        if (empty($item)) {
            throw new BadRequestHttpException('Недопустимый статус запроса');
        }

        $isShipment = false;
        $reserves = $this->getReserves($goodsRequestId);
        foreach ($reserves as $reserve) {
            if (!empty($reserve['shipment_id'])) {
                $isShipment = true;

                break;
            }
        }

        if ((!empty($item['equipment_id']) || !empty($item['goods_issue_id'])) && $isShipment) {
            if (empty($item['equipment_id'])) {
                throw new BadRequestHttpException('Невозможно отменить запрос на зарегистрированное оборудование пока оно находится в пути');
            }
            if (empty($item['goods_issue_id'])) {
                throw new BadRequestHttpException('Невозможно отменить запрос на претензионный товар пока он находится в пути');
            }
        } else {
            $query = $em->createQuery('
                UPDATE order_item 
                SET is_annuled = TRUE,
                supplier_reserve = NULL 
                WHERE
                    id = :order_item_id
            ');
            $query->setParameter('order_item_id', $goodsRequestId);
            $query->execute();

            $this->resetReserve($goodsRequestId);
        }
    }

    /**
     * Получение текущих резервов
     *
     * @param int $goodsRequestId
     *
     * @return array
     */
    public function getReserves(int $goodsRequestId) : array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery('
            SELECT
                shipment_id,
                supply_item_id,
                geo_room_id,
                reserve_type,
                SUM(delta) AS amount 
            FROM
                goods_reserve_log 
            WHERE
                goods_request_id = :goods_request_id 
            GROUP BY
                shipment_id,
                supply_item_id,
                geo_room_id,
                reserve_type 
            HAVING
                SUM(delta) > 0
        ');
        $query->setParameter('goods_request_id', $goodsRequestId);

        return $query->getArrayResult();
    }

    /**
     * Смена поставщика для обработки
     *
     * @param int $id
     * @param int $newSupplierId
     */
    public function changeSupplier(int $id, int $newSupplierId) : void
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery('
            INSERT INTO goods_request_to_supplier (supplier_id, goods_request_id, is_delayed) 
            SELECT
                supplier_id,
                id,
                TRUE 
            FROM
                goods_request 
            WHERE
                id = :id 
                AND supplier_id != :new_supplier_id ON duplicate KEY UPDATE is_delayed = TRUE
        ');
        $query->setParameter('id', $id);
        $query->setParameter('new_supplier_id', $newSupplierId);
        $query->execute();

        $query = $em->createQuery('
            UPDATE goods_request 
            SET supplier_id = :new_supplier_id, supplier_reserve = :processing 
            WHERE
                supplier_reserve IS NOT NULL 
                AND id = :id 
                AND supplier_id != :new_supplier_id        
	    ');
        $query->setParameter('id', $id);
        $query->setParameter('new_supplier_id', $newSupplierId);
        $query->setParameter('processing', SupplierReserveStatus::PROCESSING);
        $query->execute();
    }

    /**
     * Дублирование запроса
     *
     * @param int $goodsRequestId
     * @param int $newQuantity
     *
     * @return int
     */
    public function clone(int $goodsRequestId, int $newQuantity) : int
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $repository = $em->getRepository(GoodsRequest::class);
        /**
         * @var GoodsRequest $model
         */
        $model = $repository->find($goodsRequestId);

        if (!$model instanceof GoodsRequest) {
            throw new NotFoundHttpException('Запрос не найден');
        }

        if (!empty($model->getGoodsIssueId()) || !empty($model->getEquipmentId())) {
            throw new BadRequestHttpException('Существование двух активных запросов на один и тот же инвентарь или претензионный товар невозможно');
        }

        $newModel = clone $model;
        $newModel->setId(null);
        $newModel->setQuantity($newQuantity);

        $em->persist($newModel);
        $em->flush();

        return $newModel->getId();
    }

    /**
     * Добавление запроса
     *
     * @param int $baseProductId
     * @param int $quantity
     *
     * @return int
     */
    public function add(int $baseProductId, int $quantity) : int
    {
        return 1;
    }
}