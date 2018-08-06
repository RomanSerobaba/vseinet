<?php

namespace ReservesBundle\Bus\GoodsReleaseDocItem\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ReservesBundle\Entity\GoodsReleaseDocItem;
use ReservesBundle\Entity\GoodsReleaseDoc;
use Doctrine\ORM\Query\ResultSetMapping;

class DeltaCommandHandler extends MessageHandler
{
    protected $mySupplay = null;

    public function handle(DeltaCommand $command)
    {

        if (('pallet' == $command->type) && (abs($command->delta) > 1)) 
            throw new BadRequestHttpException('Нельзя оперировать более чем с одной палетой.');

        if (('pallet' == $command->type) && ('normal' != $command->goodsStateCode)) 
            throw new BadRequestHttpException('Подготовленная к отгрузке палета не может быть ненадлежащего качества.');

        $goodsReleaseDoc = $this->getDoctrine()->getManager()->getRepository(GoodsReleaseDoc::class)->find($command->goodsReleaseId);
        if (!$goodsReleaseDoc instanceof GoodsReleaseDoc) {
            throw new NotFoundHttpException('Документ не найден');
        }

        if (!empty($goodsReleaseDoc->getCompletedAt())) {
            throw new ConflictHttpException('Изменение завершенного документа невозможно.');
        }

        if (('pallet' == $command->type) && ($command->delta > 0)) {

            $this->inkPallet($command->goodsReleaseId, $command->id);

        }
        elseif (('pallet' == $command->type) && ($command->delta < 0)) {

            $this->decPallet($command->goodsReleaseId, abs($command->id));

        }
        elseif (('product' == $command->type) && ($command->delta > 0) && ('normal' == $command->goodsStateCode)) {

            $this->inkProduct($command->goodsReleaseId, $command->id, $command->delta);

        }
        elseif (('product' == $command->type) && ($command->delta < 0) && ('normal' == $command->goodsStateCode)) {

            $this->decProduct($command->goodsReleaseId, $command->id, abs($command->delta));

        }
        elseif (('product' == $command->type) && ($command->delta > 0) && ('normal' != $command->goodsStateCode)) {

            $this->mrkProduct($command->goodsReleaseId, $command->id, $command->goodsStateCode, $command->delta);

        }
        elseif (('product' == $command->type) && ($command->delta < 0) && ('normal' != $command->goodsStateCode)) {

            $this->umkProduct($command->goodsReleaseId, $command->id, $command->goodsStateCode, abs($command->delta));

        }
        else {

            throw new BadRequestHttpException('Непредвиденное сочетание параметров.');

        }

        return;
    }

    /**
     * Отгрузка паллеты
     *
     * @param int $goodsReleaseDId Идентификатор документа отгрузки
     * @param int $goodsPalletId   Паллета
     */
    private function inkPallet(int $goodsReleaseDId, int $goodsPalletId)
    {

        $queryText = "

            update
                goods_release_item
            set
                quantity = initial_quantity
            where
                goods_release_did = :goodsReleaseDId    -- Документ отгрузки
                and base_product_id is not null         -- Для работы индекса
                and goods_pallet_id = :goodsPalletId    -- Идентификатор паллеты

            returning id
        ";

        $queryResultMapping = new ResultSetMapping();
        $queryResultMapping->addScalarResult('id', 'id', 'integer');

        $queryDB = $this->getDoctrine()->getManager()
                ->createNativeQuery($queryText, $queryResultMapping)
                ->setParameters([
                    'goodsReleaseDId' => $goodsReleaseDId,
                    'goodsPalletId' => $goodsPalletId
                ]);

        $goodsReleaseItems = $queryDB->getArrayResult();

        if (0 == count($goodsReleaseItems)) {
            throw new BadRequestHttpException('Нет паллеты для отгрузки.');
        }

    }

    /**
     * Возврат (отмена отгрузки) паллеты
     *
     * @param int $goodsReleaseDId Уникальный идентификатор документа отгрузки
     * @param int $goodsPalletId   Паллета
     */
    private function decPallet(int $goodsReleaseDId, int $goodsPalletId)
    {

        $queryText = "

            update
                goods_release_item
            set
                quantity = 0
            where
                goods_release_did = :goodsReleaseDId    -- Документ отгрузки
                and base_product_id is not null         -- Для работы индекса
                and goods_pallet_id = :goodsPalletId    -- Идентификатор паллеты

            returning id
        ";

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id', 'integer');

        $queryDB = $this->getDoctrine()->getManager()
                ->createNativeQuery($queryText, $rsm)
                ->setParameters([
                    'goodsReleaseDId' => $goodsReleaseDId,
                    'goodsPalletId' => $goodsPalletId
                ]);

        $result = $queryDB->getResult();

        if (empty($result)) {
            throw new BadRequestHttpException('Нет паллеты для возврата.');
        }

    }

    /**
     * Отгрузка позиции товара
     *
     * @param int $goodsReleaseDId Идентификатор документа отгрузки
     * @param int $baseProductId  Продукт
     * @param int $delta          Количество
     */
    private function inkProduct(int $goodsReleaseDId, int $baseProductId, int $delta)
    {

        $queryText = "
            with recursive

            -- Нормализованный список отгрузки /без паллет
            normal_items (ord, id, need_quantity) as(

                select
                    row_number() over(order by o.type_code, order_item_id)
                        as ord,                                              -- Синтетический ключ нормализации
                    gri.id,
                    gri.initial_quantity - gri.quantity as need_quantity
                from
                    goods_release_item gri
                inner join order_item oi
                    on oi.id = gri.order_item_id
                inner join \"order\" o
                    on o.id = oi.order_id
                where
                    gri.goods_release_did = :goodsReleaseDId                 -- Документ отгрузки
                    and gri.base_product_id = :baseProductId                 -- Идентификатор продукта
                    and gri.goods_pallet_id is null                          -- Идентификатор отсутствия паллеты
                    and gri.goods_state_code = 'normal'::goods_state_code    -- Без претензии
                    and gri.initial_quantity > gri.quantity                  -- Не отгружен или отгружен частично
                order by
                    o.type_code,
                    gri.order_item_id

            ),

            proporcional(ord, id, add_quantity, rest) as (

                select r0.ord, r0.id, r0.add_quantity, r0.rest from (

                    select
                        ni1.ord,
                        ni1.id,
                        least(ni1.need_quantity, {$delta}) as add_quantity,
                        {$delta} - least(ni1.need_quantity, {$delta}) as rest
                    from
                        normal_items ni1
                    order by ni1.ord
                    limit 1

                ) r0

                union all

                select r1.ord, r1.id, r1.add_quantity, r1.rest from (

                    select
                        ni2.ord,
                        ni2.id,
                        least(ni2.need_quantity, pr1.rest) as add_quantity,
                        pr1.rest - least(ni2.need_quantity, pr1.rest) as rest
                    from
                        normal_items ni2,
                        proporcional pr1
                    where
                        ni2.ord > pr1.ord and pr1.rest > 0
                    order by ni2.ord
                    limit 1

                ) r1

            )

            update
                goods_release_item grif
            set
                quantity = grif.quantity + ppc.add_quantity
            from
                (select id, add_quantity from proporcional) ppc
            where
                grif.id = ppc.id
                and 0 = (select min(pp2.rest) from proporcional pp2)

            returning grif.id
        ";

        $queryResultMapping = new ResultSetMapping();
        $queryResultMapping->addScalarResult('id', 'id', 'integer');

        $queryDB = $this->getDoctrine()->getManager()
                ->createNativeQuery($queryText, $queryResultMapping)
                ->setParameters([
                    'goodsReleaseDId' => $goodsReleaseDId,
                    'baseProductId' => $baseProductId,
                ]);

        $goodsReleaseItems = $queryDB->getArrayResult();

        if (0 == count($goodsReleaseItems)) {
            throw new BadRequestHttpException('Нет товара для отгрузки.');
        }

    }

    /**
     * Возврат (отмена отгрузки) позиции товара
     *
     * @param int $goodsReleaseDId Идентификатор документа отгрузки
     * @param int $baseProductId   Продукт
     * @param int $delta           Количество
     */
    private function decProduct(int $goodsReleaseDId, int $baseProductId, int $delta)
    {

        $queryText = "
            with recursive

            -- Нормализованный список отгрузки /без паллет
            normal_items (ord, id, quantity) as(

                select
                    row_number() over(order by o.type_code  DESC, order_item_id  DESC)
                        as ord,                                              -- Синтетический ключ нормализации
                    gri.id,
                    gri.quantity as quantity
                from
                    goods_release_item gri
                inner join order_item oi
                    on oi.id = gri.order_item_id
                inner join \"order\" o
                    on o.id = oi.order_id
                where
                    gri.goods_release_did = :goodsReleaseDId                 -- Документ отгрузки
                    and gri.base_product_id = :baseProductId                 -- Идентификатор продукта
                    and gri.goods_pallet_id is null                          -- Идентификатор отсутствия паллеты
                    and gri.goods_state_code = 'normal'::goods_state_code    -- Без претензии
                    and gri.quantity > 0                                     -- Отгружен полностью или частично
                order by
                    o.type_code DESC,
                    gri.order_item_id  DESC
            ),

            proporcional(ord, id, rmv_quantity, rest) as (

                select r0.ord, r0.id, r0.rmv_quantity, r0.rest from (

                    select
                        ni1.ord,
                        ni1.id,
                        least(ni1.quantity, {$delta}) as rmv_quantity,
                        {$delta} - least(ni1.quantity, {$delta}) as rest
                    from
                        normal_items ni1
                    order by ni1.ord
                    limit 1

                ) r0

                union all

                select r1.ord, r1.id, r1.rmv_quantity, r1.rest from (

                    select
                        ni2.ord,
                        ni2.id,
                        least(ni2.quantity, pr1.rest) as rmv_quantity,
                        pr1.rest - least(ni2.quantity, pr1.rest) as rest
                    from
                        normal_items ni2,
                        proporcional pr1
                    where
                        ni2.ord > pr1.ord and pr1.rest > 0
                    order by ni2.ord
                    limit 1

                ) r1

            )

            update
                goods_release_item grif
            set
                quantity = grif.quantity - ppc.rmv_quantity
            from
                (select id, rmv_quantity from proporcional) ppc
            where
                grif.id = ppc.id
                and 0 = (select min(pp2.rest) from proporcional pp2)

            returning grif.id
        ";

        $queryResultMapping = new ResultSetMapping();
        $queryResultMapping->addScalarResult('id', 'id', 'integer');

        $queryDB = $this->getDoctrine()->getManager()
                ->createNativeQuery($queryText, $queryResultMapping)
                ->setParameters([
                    'goodsReleaseDId' => $goodsReleaseDId,
                    'baseProductId' => $baseProductId,
                ]);

        $goodsReleaseItems = $queryDB->getArrayResult();

        if (0 == count($goodsReleaseItems)) {
            throw new BadRequestHttpException('Нет товара для возврата.');
        }

    }

    /**
     * Отметка позиции товара как некачественной (для помещения в претензию, без отгрузки)
     *
     * @param int    $goodsReleaseDId Идентификатор документа отгрузки
     * @param int    $baseProductId  Продукт
     * @param string $goodsStateCode Вариант дефекта товара
     * @param int    $delta          Количество
     */
    private function mrkProduct(int $goodsReleaseDId, int $baseProductId, string $goodsStateCode, int $delta)
    {

        $queryText = "
            -- Нормализованный список отгрузки без паллет
            select
                row_number() over(order by o.type_code  DESC, order_item_id  DESC)
                    as ord,                                              -- Синтетический ключ нормализации
                gri.id,
                gri.initial_quantity as initial_quantity,
                gri.quantity as quantity,
                gri.order_item_id as order_item_id
            from
                goods_release_item gri
            inner join order_item oi
                on oi.id = gri.order_item_id
            inner join \"order\" o
                on o.id = oi.order_id
            where
                gri.goods_release_did = {$goodsReleaseDId}               -- Документ отгрузки
                and gri.base_product_id = {$baseProductId}               -- Идентификатор продукта
                and gri.goods_pallet_id is null                          -- Идентификатор отсутствия паллеты
                and gri.goods_state_code = 'normal'::goods_state_code    -- Без претензии
                and gri.quantity < gri.initial_quantity                  -- Не отгружен или отгружен частично
            order by
                o.type_code DESC,
                gri.order_item_id  DESC
        ";

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('ord', 'ord', 'integer');
        $rsm->addScalarResult('id', 'id', 'integer');
        $rsm->addScalarResult('initial_quantity', 'initialQuantity', 'integer');
        $rsm->addScalarResult('quantity', 'quantity', 'integer');
        $rsm->addScalarResult('order_item_id', 'orderItemId', 'integer');

        $em = $this->getDoctrine()->getManager();

        $goodsReleaseItems = $em
                ->createNativeQuery($queryText, $rsm)
                ->getArrayResult();

        foreach ($goodsReleaseItems as $goodsReleaseItem) {

            // Количество товара, отбракованного в этой строке
            $markQuantity = ($delta <= ($goodsReleaseItem['initialQuantity'] - $goodsReleaseItem['quantity'])) ? 
                    $delta : 
                    ($goodsReleaseItem['initialQuantity'] - $goodsReleaseItem['quantity']);

            $griSource = $em->getRepository(GoodsReleaseDocItem::class)->find($goodsReleaseItem['id']);

            $griRecipient = $em->getRepository(GoodsReleaseDocItem::class)->findOneBy([
                'goodsReleaseDId' => $goodsReleaseDId,
                'baseProductId' => $baseProductId,
                'goodsStateCode' => $goodsStateCode,
                'orderItemId' => $goodsReleaseItem['orderItemId'],
                ]);

            if ($griRecipient instanceof GoodsReleaseDocItem) {

                // приемник для бракованного отовара найден
                
                $griRecipient->setQuantity($griRecipient->getQuantity() + $markQuantity);
                $griRecipient->setInitialQuantity($griRecipient->getInitialQuantity() + $markQuantity);

                $em->persist($griRecipient);

                if ((0 == $goodsReleaseItem['quantity']) && ($goodsReleaseItem['initialQuantity'] == $markQuantity)) {

                    // вся строка ушла в брак
                    $em->remove($griSource);

                }else{

                    // Уменьшим количество в строке на сумму перемещённую в брак

                    $griSource->setInitialQuantity($goodsReleaseItem['initialQuantity'] - $markQuantity);

                    $em->persist($griSource);

                }

            }else{

                // приемник для бракованного отовара не найден

                if (0 == $goodsReleaseItem['quantity'] && $goodsReleaseItem['initialQuantity'] == $markQuantity) {

                    // вся строка ушла в брак

                    $griSource->setGoodsStateCode($goodsStateCode);
                    $griSource->setQuantity($markQuantity);
                    $em->persist($griSource);

                }else{

                    // Создадим новый приёмник брака

                    $griRecipient = clone $griSource;
                    $griRecipient->setGoodsStateCode($goodsStateCode);
                    $griRecipient->setInitialQuantity($markQuantity);
                    $griRecipient->setQuantity($markQuantity);

                    $em->persist($griRecipient);

                    // Уменьшим количество в строке на количество перемещённое в брак

                    $griSource->setInitialQuantity($goodsReleaseItem['initialQuantity'] - $markQuantity);

                    $em->persist($griSource);
                }

            }

            $em->flush();
            $delta -= $markQuantity;
            
            if (0 == $delta) break;

        }

        if (0 != $delta) {
            throw new BadRequestHttpException('Невозможно пометить указанное количество товара как некачественный.');
        }

    }


    /**
     * Отмена отметки позиции товара как некачественной (для возможности дальнейшей отгрузки отгрузки)
     *
     * @param int    $goodsReleaseDId Идентификатор документа отгрузки
     * @param int    $baseProductId  Продукт
     * @param string $goodsStateCode Вариант дефекта товара
     * @param int    $delta          Количество
     */
    private function umkProduct(int $goodsReleaseDId, int $baseProductId, string $goodsStateCode, int $delta)
    {

        $queryText = "
            -- Нормализованный список отгрузки без паллет
            select
                row_number() over(order by o.type_code, order_item_id)
                    as ord,                                              -- Синтетический ключ нормализации
                gri.id,
                gri.initial_quantity as initial_quantity,
                gri.quantity as quantity,
                gri.order_item_id as order_item_id
            from
                goods_release_item gri
            inner join order_item oi
                on oi.id = gri.order_item_id
            inner join \"order\" o
                on o.id = oi.order_id
            where
                gri.goods_release_did = {$goodsReleaseDId}                       -- Документ отгрузки
                and gri.base_product_id = {$baseProductId}                       -- Идентификатор продукта
                and gri.goods_pallet_id is null                                  -- Идентификатор отсутствия паллеты
                and gri.goods_state_code = '{$goodsStateCode}'::goods_state_code -- Тип дефекта
                and gri.quantity > 0                                             -- Не отгружен или отгружен частично
            order by
                o.type_code,
                gri.order_item_id
        ";

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('ord', 'ord', 'integer');
        $rsm->addScalarResult('id', 'id', 'integer');
        $rsm->addScalarResult('quantity', 'quantity', 'integer');
        $rsm->addScalarResult('initial_quantity', 'initialQuantity', 'integer');
        $rsm->addScalarResult('order_item_id', 'orderItemId', 'integer');

        $em = $this->getDoctrine()->getManager();

        $goodsReleaseItems = $em
                ->createNativeQuery($queryText, $rsm)
                ->getArrayResult();

        foreach ($goodsReleaseItems as $goodsReleaseItem) {

            $griSource = $em->getRepository(GoodsReleaseDocItem::class)->find($goodsReleaseItem['id']);

            $unmarkQuantity = ($delta <= $goodsReleaseItem['quantity']) ? $delta : $goodsReleaseItem['quantity'];

            $griRecipient = $em->getRepository(GoodsReleaseDocItem::class)->findOneBy([
                'goodsReleaseDId' => $goodsReleaseDId,
                'baseProductId' => $baseProductId,
                'goodsStateCode' => 'normal',
                'orderItemId' => $goodsReleaseItem['orderItemId'],
                ]);

            if ($griRecipient instanceof GoodsReleaseDocItem) {

                // приемник для бракованного отовара найден

                // переместим в строку приемник бракованный товар

                //$griRecipient->setQuantity($griRecipient->getQuantity() + $unmarkQuantity);
                $griRecipient->setInitialQuantity($griRecipient->getInitialQuantity() + $unmarkQuantity);

                $em->persist($griRecipient);

                if ($goodsReleaseItem['quantity'] == $unmarkQuantity) {

                    // Строка "посчитана". Удаляем строку.
                    $em->remove($griSource);

                }else{

                    // Уменьшим количество в строке на сумму перемещённую в брак

                    $griSource->setInitialQuantity($goodsReleaseItem['initialQuantity'] - $unmarkQuantity);
                    $griSource->setQuantity($goodsReleaseItem['quantity'] - $unmarkQuantity);

                    $em->persist($griSource);

                }

            }else{

                // приемник для бракованного отовара не найден

                if ($goodsReleaseItem['quantity'] == $unmarkQuantity) {

                    // вся строка вернулась из брака

                    $griSource->setGoodsStateCode();
                    $griSource->setQuantity();
                    $em->persist($griSource);

                }else{

                    // Создадим новый приёмник

                    $griRecipient = clone $griSource;
                    $griRecipient->setGoodsStateCode();
                    $griRecipient->setInitialQuantity($unmarkQuantity);
                    $griRecipient->setQuantity();

                    $em->persist($griRecipient);

                    // Уменьшим количество в строке на количество вернувшееся из брака

                    $griSource->setInitialQuantity($goodsReleaseItem['initialQuantity'] - $unmarkQuantity);
                    $griSource->setQuantity($goodsReleaseItem['quantity'] - $unmarkQuantity);

                    $em->persist($griSource);
                }

            }

            $em->flush();
            $delta -= $unmarkQuantity;

            if (0 == $delta) break;

        }

        if (0 != $delta) {
            throw new BadRequestHttpException('Невозможно пометить указанное количество товара как новый.');
        }

    }

}
