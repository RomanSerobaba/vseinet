<?php

namespace ReservesBundle\Bus\GoodsPackaging\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\Query\ResultSetMapping;
use ReservesBundle\Entity\GoodsPackaging;

class UnRegistrationCommandHandler extends MessageHandler
{
    protected $mySupplay = null;
    
    public function handle(UnRegistrationCommand $command) 
    {
        
        $em = $this->getDoctrine()->getManager();
        
        $goodsPackaging = $em->getRepository(GoodsPackaging::class)->find($command->id);
        if (!$goodsPackaging instanceof GoodsPackaging) {
            throw new NotFoundHttpException('Документ комплектации/разкомплектации не найден');
        }
        
        // Удаляем старые записи из движений товаров

        $rsm = new ResultSetMapping();

        $queryText = "
            delete from goods_reserve_register
            where
                registrator_type_code = '". \AppBundle\Enum\DocumentTypeCode::GOODS_PACKAGING ."'::operation_type_code and
                document_id = :goodsPackagingId
        ";

        $queryDB = $em->createNativeQuery($queryText, $rsm)
                ->setParameters(['goodsPackagingId' => $command->id]);

        $queryDB->execute();

        // Удаляем старые записи из партий

        $queryText = "
            delete from supply_item
            where
                goods_packaging_id = :goodsPackagingId
        ";

        $queryDB = $em->createNativeQuery($queryText, $rsm)
                ->setParameters(['goodsPackagingId' => $command->id]);

        $queryDB->execute();

        // Запись шапки документа
        
        $goodsPackaging->setRegistredAt(null);

        $em->persist($goodsPackaging);
        $em->flush();
    }
    
}
