<?php

namespace ReservesBundle\Bus\GoodsReleaseDoc;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Doctrine\ORM\Query\ResultSetMapping;
use ReservesBundle\Entity\GoodsReleaseDoc;
use AppBundle\Enum\GoodsReleaseType;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class GoodsReleaseDocUpdate
{

    /**
     * Отмена регистрации документа в учетной системе.
     *
     * @param \ReservesBundle\Entity\GoodsReleaseDoc $newDocument регистрируемый документ
     * @param \ReservesBundle\Entity\GoodsReleaseDoc $oldDocument старый регистрируемый документ
     * @param \Doctrine\ORM\entityManager            $em          менеджер сущностей
     * @param \AppBundle\Entity\User                 $currentUser пользователь, регистрирующий документ
     */
    public static function Update(GoodsReleaseDoc $newDocument, GoodsReleaseDoc $oldDocument, \Doctrine\ORM\entityManager $em, \AppBundle\Entity\User $currentUser)
    {

        // Общие проверки документа на корректность заполнения

        if (!empty($oldDocument->getCompletedAt()))
            throw new ConflictHttpException('Изменение завершенного документа невозможно.');

        if (!empty($oldDocument->getRegisteredAt()))
            throw new ConflictHttpException('Изменение зарегистрированного документа невозможно.');

        if ($newDocument->getStatusCode() != $oldDocument->getStatusCode()) {

            // Проверка допустимости измененения статуса документа

            $rsm = new ResultSetMapping();
            $rsm->addScalarResult("name", "name", "string");
            $rsm->addScalarResult("status_code", "statusCode", "string");
            $rsm->addScalarResult("active", "active", "boolean");
            $rsm->addScalarResult("completing", "completing", "boolean");
            $rsm->addScalarResult("available_new_status_code", "availableNewStatusCode", "string");

            $statusQeryText = "
                select
                    name,
                    status_code,
                    active,
                    completing,
                    available_new_status_code
                from goods_release_doc_status
                where
                    status_code = :statusCode
                ";

            $newStatus = $em->createNativeQuery($statusQeryText, $rsm)
                    ->setParameter('statusCode', $newDocument->getStatusCode())
                    ->getOneOrNullResult();

            if (empty($newStatus))
                throw new BadRequestHttpException('Новый статус не соответствует документу');

            $oldStatus = $em->createNativeQuery($statusQeryText, $rsm)
                    ->setParameter('statusCode', $oldDocument->getStatusCode())
                    ->getOneOrNullResult();

            if (empty($oldStatus))
                throw new BadRequestHttpException('Старый статус не соответствует документу');

            // проверка возможности перехода на новый статус
            if (!preg_match("/\W" . $newStatus['statusCode'] . "\W/", $oldStatus['availableNewStatusCode'])) {
                throw new ConflictHttpException('Недопустимый новый статус документа');
            }

            if ($newStatus['completing']) {

                $newDocument->setCompletedAt(new \DateTime);
                $newDocument->setCompletedBy($currentUser->getId());
            } else {

                $newDocument->setCompletedAt();
                $newDocument->setCompletedBy();
            }
        }

        // Особые проверки документа на корректность заполнения

        if (GoodsReleaseType::MOVEMENT == $newDocument->getGoodsReleaseType() && empty($newDocument->getDestinationRoomId()))
            throw new BadRequestHttpException('При типе заказа "внутреннее перемещение" обязательно должен быть указан склад-приёмник.');

        if (GoodsReleaseType::TRANSIT == $newDocument->getGoodsReleaseType() && empty($newDocument->getDestinationRoomId()))
            throw new BadRequestHttpException('При типе заказа "перемещение" обязательно должен быть указан склад-приёмник.');

        // Особая проверка законченности отгрузки
        
        if (!empty($newDocument->getCompletedAt())) {
        
            //////////////////////////////////////////////////////
            //
            //  Проверка, весь-ли товар обработан
            //  Посмотрим на разницу initialQuantity и quantity
            //

            $result = $em->createQuery("
                        SELECT
                            SUM(i.initialQuantity - (case when i.quantity is null then 0 else i.quantity end)) as delta
                        FROM ReservesBundle\Entity\GoodsReleaseDocItem as i
                        WHERE
                            i.goodsReleaseDId = :goodsReleaseDId")
                    ->setParameters(['goodsReleaseDId' => $newDocument->getDId()])
                    ->getOneOrNullResult();

            if (!empty($result)) {
                if ($result['delta'] > 0) {
                    throw new ConflictHttpException('Не весь товар обработан. Количество не обработанных единиц товара: '. $result['delta']);
                }
                if ($result['delta'] < 0) {
                    throw new ConflictHttpException('Обработанно товара больше, чем затребованно. Количество излишне обработанных единиц товара: '. abs($result['delta']));
                }
            }
        
        }
        
        $em->persist($newDocument);
        $em->flush();
    }

}
