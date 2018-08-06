<?php

namespace ReservesBundle\Bus\GoodsAcceptance\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ReservesBundle\Entity\GoodsAcceptance;
use Doctrine\ORM\Query\ResultSetMapping;

class CreateFromSupplyCommandHandler extends MessageHandler
{
    private $supplyiesDocumentsIds = [];
    private $supplyiesDocumentsNumbers = [];
    private $documentNumber = 0;

    public function handle(CreateFromSupplyCommand $command) 
    {
        //ToDo: Оптимизировать после переделки закупок на новый шаблон
        
        $em = $this->getDoctrine()->getManager();
        
        $this->documentIDsNormalise($em, $command);
                
        if (!$this->checkDoubleSupplyiesDocumentsIds($em))
            throw new BadRequestHttpException('');
        
        // Создание документа
        
        $this->newNumber($em);
        
        $currentUser = $this->get('user.identity')->getUser();

        $document = new GoodsAcceptance();
        
        $document->setNumber($this->documentNumber);
        $document->setGeoRoomId($command->geoRoomId);
        $document->setSupplyiesDocumentsIds($this->supplyiesDocumentsIds);
        $document->setStatusCode(GoodsAcceptance::STATUS_NEW);
        
        $document->setCreatedAt(new \DateTime());        
        $document->setCreatedBy($currentUser->getId());
        
        if (!empty($command->title)) {
            $document->setTitle($command->title);
        }else{
            if (empty($command->geoRoomSource)) {
                $document->setTitle('Поступление №'. $this->documentNumber);
            }else{
                $document->setTitle('Транзит №'. $this->documentNumber);
            }
        }
        
        $em->persist($document);
        $em->flush();
        
        $this->get('uuid.manager')->saveId($command->uuid, $document->getDId());
        
        // Заполняем список продуктов
        
        $goodsAcceptanceDId = $document->getDId();
        
        $rsm = new ResultSetMapping();

        $queryText = "
            
            with all_products as(
                select 
                    grr.base_product_id,
                    grr.order_item_id,
                    grr.supply_item_id,
                    sum(grr.delta) as quantity
                from goods_reserve_register grr
                where
                    grr.registrator_id in (". implode(",", $this->supplyiesDocumentsNumbers) .") and
                    grr.registrator_type_code = 'supply'::document_type_code
                group by
                    grr.base_product_id,
                    grr.order_item_id,
                    grr.supply_item_id
            )

            insert into goods_acceptance_doc_item (
                goods_acceptance_did,
                goods_state_code,
                goods_pallet_id,
                quantity,
                base_product_id,
                order_item_id,
                supply_item_id,
                initial_quantity
            ) select 
                {$goodsAcceptanceDId},
                'normal'::goods_state_code,
                null,
                0,
                ap.base_product_id,
                ap.order_item_id,
                ap.supply_item_id,
                ap.quantity
            from all_products ap
        ";

        $result = $em->createNativeQuery($queryText, new ResultSetMapping())->execute();
        
    }
    
    private function documentIDsNormalise($em, $command)
    {
        
        $this->supplyiesDocumentsIds = [];
        $this->supplyiesDocumentsNumbers = [];
        
        if (empty($command->suppliesDocumentsNumbers)) {
            
            if (empty($command->supplyiesDocumentsIds))
                throw new BadRequestHttpException('Должен быть указан список документов источников');
            
            $this->supplyiesDocumentsIds = $command->supplyiesDocumentsIds;

            $queryText = "

                select
                    number
                from supply_doc
                where
                    did in (". implode(',', $command->supplyiesDocumentsIds) .")
                        
            ";

            $rsm = new ResultSetMapping();
            $rsm->addScalarResult('number', 'number', 'integer');

            $items = $em
                    ->createNativeQuery($queryText, $rsm)
                    ->getResult();

            if (empty($items))
                throw new BadRequestHttpException('Документы-источники не найдены');
            
            foreach ($items as $item) {
                
                $this->supplyiesDocumentsNumbers[] = $item['number'];
                
            }
            
        }else{
            
            $this->supplyiesDocumentsNumbers = $command->suppliesDocumentsNumbers;
            
            $queryText = "

                select
                    did
                from supply_doc
                where
                    number in (". implode(',', $command->suppliesDocumentsNumbers) .")
                        
            ";

            $rsm = new ResultSetMapping();
            $rsm->addScalarResult('did', 'did', 'integer');

            $items = $em
                    ->createNativeQuery($queryText, $rsm)
                    ->getResult();

            if (empty($items))
                throw new BadRequestHttpException('Документы-источники не найдены');
            
            foreach ($items as $item) {
                
                $this->supplyiesDocumentsIds[] = $item['ids'];
                
            }
            
        }
        
    }
    
    // Получение номера документа
    private function newNumber($em)
    {

        $queryText = "select nextval('goods_acceptance_doc_number'::regclass) as id;";

        $result = $em->createNativeQuery($queryText, new ResultSetMapping())
                ->getResult('ListHydrator');
        
        $this->documentNumber = (int)$result[0];

    }
    
    // Проверка на повторное использование закупок
    private function checkDoubleSupplyiesDocumentsIds($em): bool
    {
        //ToDo: сделать проверку на повторное использование закупок
        return true;
    }
}