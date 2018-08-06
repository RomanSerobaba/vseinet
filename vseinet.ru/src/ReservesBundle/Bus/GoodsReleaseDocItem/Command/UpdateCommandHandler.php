<?php

namespace ReservesBundle\Bus\GoodsReleaseDocItem\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ReservesBundle\Entity\GoodsReleaseDocItem;

class UpdateCommandHandler extends MessageHandler
{
    protected $mySupplay = null;

    public function handle(UpdateCommand $command)
    {
        
        if (0 > $command->quantity) {
            throw new BadRequestHttpException('Количество отгруженного товара не может быть отрицательным.');
        }
            
        $em = $this->getDoctrine()->getManager();

        $goodsRelease = $em->getRepository(GoodsRelease::class)->find($command->id);
        if (!$goodsRelease instanceof GoodsRelease) {
            throw new NotFoundHttpException('Документ выдачи товара не найден');
        }
        
        // Проверка статуса документа в базе данных
        if (!empty($goodsRelease->getCompletedAt())) {
            throw new ConflictHttpException('Изменение завершенного документа невозможно.');
        }

        $goodsReleaseItem = $em->getRepository(GoodsReleaseDocItem::class)->findOneBy([
            'goodsReleaseId' => $command->goodsReleaseId,
            'id' => $command->id
        ]);

        if (!$goodsReleaseItem instanceof GoodsReleaseDocItem) {
            throw new NotFoundHttpException('Элемент списка не найден');
        }

        if ($goodsReleaseItem->getInitialQuantity() < $command->quantity) {

            throw new ConflictHttpException('Нельзя отгрузить больше запрошенного количества.');

        } else if (0 < $command->quantity) {

            $goodsReleaseItem->setQuantity($command->quantity);
            $goodsReleaseItem->setDefectType($command->defectType);

            $em->persist($goodsReleaseItem);
            $em->flush();

        } else {

            $addInitialQuantity = 0;
            
            // Возможно нужно удалить строку

            $queryText = "

                select
                    i.id
                from ReservesBundle\Entity\GoodsReleaseDocItem as i
                where
                    i.goodsReleaseId = :goodsReleaseId and
                    i.orderItemId = :orderItemId and
                    i.baseProductId = :baseProductId and
                    i.goodsPalletId = :goodsPalletId and
                    i.id <> :id
                order by i.defectType DESC
            ";

            $queryDB = $this->getDoctrine()->getManager()
                    ->createQuery($queryText)
                    ->setMaxResults(1)
                    ->setParameters([
                        'goodsReleaseId' => $command->goodsReleaseId,
                        'orderItemId' => $goodsReleaseItem->getOrderItemId(),
                        'baseProductId' => $goodsReleaseItem->getBaseProductId(),
                        'goodsPalletId' => $goodsReleaseItem->getGoodsPalletId(),
                        'id' => $command->id
                    ]);

            $results = $queryDB->getArrayResult();
            
            if (count($results) > 0) {
                
                $addInitialQuantity = $goodsReleaseItem->getInitialQuantity();
                
                $em->remove($goodsReleaseItem);
                $em->flush();
                
                $goodsReleaseItem = $em->getRepository(GoodsReleaseDocItem::class)->find($results[0]['id']);
                
                $goodsReleaseItem->setInitialQuantity($goodsReleaseItem->getInitialQuantity() + $addInitialQuantity);


            } else {

                $goodsReleaseItem->setQuantity($command->quantity);
                $goodsReleaseItem->setDefectType($command->defectType);
                
            }
            
            $em->persist($goodsReleaseItem);
            $em->flush();
                    
        }

        return;
    }

}
