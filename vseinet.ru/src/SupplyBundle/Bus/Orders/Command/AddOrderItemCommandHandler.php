<?php 

namespace SupplyBundle\Bus\Orders\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\ProductAvailabilityCode;
use AppBundle\Enum\SupplierReserveStatus;
use SupplyBundle\Entity\ProductAvailability;
use OrderBundle\Entity\OrderItem;
use OrderBundle\Entity\ClientOrderItem;
use RegisterBundle\Entity\GoodsNeedRegister;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\Query\ResultSetMapping;

class AddOrderItemCommandHandler extends MessageHandler
{
    public function handle(AddOrderItemCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        if ($command->quantity <= 0) {
            throw new BadRequestHttpException('Недопустимое количество');
        }

        $requiredPrepayment = 0;

        $query = $em->createQuery('
            SELECT
                bp.id, 
                p2.price,
                p2.productAvailabilityCode,
                bp.supplierId
            FROM
                OrderBundle:OrderDoc AS o
                JOIN ContentBundle:BaseProduct AS bp WITH bp.id = :base_product_id
                LEFT JOIN PricingBundle:Product AS p WITH p.baseProductId = bp.id AND o.geoCityId = p.geoCityId
                JOIN PricingBundle:Product AS p2 WITH p2.baseProductId = bp.id AND (p2.geoCityId = p.geoCityId OR p.geoCityId IS NULL AND p2.geoCityId IS NULL)
            WHERE
                o.number = :order_id
        ');
        $query->setParameter('order_id', $command->id);
        $query->setParameter('base_product_id', $command->baseProductId);

        $baseProduct = $query->getOneOrNullResult();

        if (empty($baseProduct)) {
            throw new NotFoundHttpException('Добавляемый товар не найден');
        }

        if (empty($baseProduct['price'])) {
            throw new NotFoundHttpException('У добавляемого товара не найдена цена');
        }

        $orderItem = new OrderItem();
        $orderItem->setOrderId($command->id);
        $orderItem->setBaseProductId($command->baseProductId);
        $orderItem->setQuantity($command->quantity);
        $orderItem->setCreatedAt(new \DateTime(date('Y-m-d H:i:s')));
        $orderItem->setCreatedBy($this->get('user.identity')->getUser()->getId());
        $em->persist($orderItem);
        $em->flush();

        if (!$orderItem->getId()) {
            throw new BadRequestHttpException('Позиция не добавлена');
        }


        $clientOrderItem = new ClientOrderItem();
        $clientOrderItem->setOrderItemId($orderItem->getId());
        $clientOrderItem->setRetailPrice($baseProduct['price']);
        $clientOrderItem->setInitialRetailPrice($baseProduct['price']);
        $clientOrderItem->setRequiredPrepayment(0);
        $clientOrderItem->setReservedPrepayment(0);
        $em->persist($clientOrderItem);
        $em->flush();

        $register = new GoodsNeedRegister();
        $register->setOrderItemId($orderItem->getId());
        $register->setBaseProductId($command->baseProductId);
        $register->setDelta($command->quantity);
        $register->setRegisteredAt(new \DateTime(date('Y-m-d H:i:s')));
        $register->setRegistratorId($command->id);
        $register->setRegisterOperationTypeCode('order_creation');
        $register->setRegistratorTypeCode('order');
        $em->persist($register);
        $em->flush();
        /**
         * Рассчитываем required_prepayment (round ( 30% * order_item.retail_price, -3 )) на основании процента отказов клиента ( >20% ) и риска по категории
         */

        // Отмененные заказы
        $query = $em->createQuery('
            SELECT
                COUNT(DISTINCT oi.id)
            FROM
                OrderBundle:OrderAnnulItem AS oai
                JOIN OrderBundle:OrderAnnul AS oa WITH oa.id = oai.orderAnnulId
                JOIN OrderBundle:OrderItem AS oi WITH oi.id = oai.orderItemId
                JOIN OrderBundle:OrderDoc AS o WITH o.number = oi.orderId 
                JOIN OrderBundle:ClientOrder AS co WITH co.orderId = o.number 
            WHERE
                co.userId = :user_id
                AND oa.isClientOffender = TRUE
        ');
        $query->setParameter('user_id', $command->userId);
        $annulledCount = $query->getSingleScalarResult();

        // Завершенные заказы
        $query = $em->createQuery('
            SELECT
                COUNT(DISTINCT sr.orderItemId)
            FROM
                OrderBundle:SalesRegister AS sr
                JOIN OrderBundle:OrderItem AS oi WITH oi.id = sr.orderItemId
                JOIN OrderBundle:ClientOrder AS co WITH co.orderId = oi.orderId 
            WHERE
                co.userId = :user_id
        ');
        $query->setParameter('user_id', $command->userId);
        $completedCount = $query->getSingleScalarResult();

        // Рискованный товар
        $query = $em->createQuery('
            SELECT
                COALESCE(c.isRisky, FALSE) AS isRisky
            FROM
                OrderBundle:OrderItem AS oi
                JOIN ContentBundle:BaseProduct AS bp WITH bp.id = oi.baseProductId
                JOIN ContentBundle:CategoryPath AS cp WITH cp.id = bp.categoryId
                JOIN ContentBundle:Category AS c WITH c.id = cp.pid 
            WHERE
                oi.id = :order_item_id
                AND c.isRisky = TRUE 
        ', new ResultSetMapping());
        $query->setParameter('order_item_id', $command->userId);
        $riskyRow = $query->getFirstResult();
// print_r($riskyRow);die();
        $percent = 0;
        if ($completedCount) {
            $percent = ceil($annulledCount / $completedCount * 100);
        }

        if ($percent > OrderItem::ANNULLED_PERCENT || !empty($riskyRow['is_risky'])) {
            $requiredPrepayment = ceil($baseProduct['price'] * OrderItem::REQUIRED_PREPAYMENT_PERCENT / 100);
        }

        // Требуется предоплата
        if ($requiredPrepayment > 0) {
            $query = $em->createQuery('
                UPDATE order_item SET 
                    required_prepayment = :required_prepayment
                WHERE
                    id = :order_item_id
            ');
            $query->setParameter('required_prepayment', $requiredPrepayment);
            $query->setParameter('order_item_id', $orderItemID);

            $query->execute();
        }
        $this->get('uuid.manager')->saveId($command->uuid, $orderItem->getId());
    }
}