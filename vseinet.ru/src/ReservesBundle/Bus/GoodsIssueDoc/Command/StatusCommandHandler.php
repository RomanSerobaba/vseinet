<?php

namespace ReservesBundle\Bus\GoodsIssueDoc\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ReservesBundle\Entity\GoodsIssueDoc;
use ReservesBundle\Bus\GoodsIssueDoc\GoodIssueDocRegistration;
use ReservesBundle\Bus\GoodsIssueDoc\GoodIssueDocUnRegistration;
use Doctrine\ORM\Query\ResultSetMapping;

class StatusCommandHandler extends MessageHandler
{
    protected $mySupplay = null;
    
    public function handle(StatusCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();
        $currentUser = $this->get('user.identity')->getUser();
        
        $document = $em->getRepository(GoodsIssueDoc::class)->find($command->id);
        if (!$document instanceof GoodsIssueDoc) {
            throw new NotFoundHttpException('Документ не найден (команда)');
        }

        if ($document->getStatusCode() == $command->statusCode) return;

        if (!empty($document->getCompletedAt())) {
            throw new ConflictHttpException('Завершённый документ невозможно изменить');
        }        

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult("name", "name", "string");
        $rsm->addScalarResult("status_code", "statusCode", "string");
        $rsm->addScalarResult("active", "active", "boolean");
        $rsm->addScalarResult("completing", "completing", "boolean");
        $rsm->addScalarResult("available_new_status_code", "availableNewStatusCode", "string");
        
        $newStatus = $em->createNativeQuery("
            select
                name,
                status_code,
                active,
                completing,
                available_new_status_code
            from goods_issue_doc_status
            where 
                status_code = '{$command->statusCode}'::any_doc_status_code
            limit 1
            ", $rsm)->getResult();
        
        if (empty($newStatus)) {
            throw new BadRequestHttpException('Статус не соответствует документу');
        }
        $newStatus = $newStatus[0];

        if (!$newStatus['active']) {
            throw new ConflictHttpException('Новый статус документа недоступен для использования');
        }
        
        $curStatus = $em->createNativeQuery("
            select
                name,
                status_code,
                active,
                completing,
                available_new_status_code
            from goods_issue_doc_status
            where 
                status_code = '{$document->getStatusCode()}'::any_doc_status_code
            limit 1
            ", $rsm)->getResult();
        
        if (empty($curStatus)) {
            throw new ConflictHttpException('Неожиданный текущий статус документа');
        }
        $curStatus = $curStatus[0];
        
        // проверка возможности перехода на новый статус
        if (!preg_match("/\W". $newStatus['statusCode'] ."\W/", $curStatus['availableNewStatusCode'])) {
            throw new ConflictHttpException('Неожиданный новый статус документа');
        }

        GoodIssueDocUnRegistration::unRegistration($document, $em, $currentUser);
                
        $document->setStatusCode($newStatus['statusCode']);
        
        if (!empty($newStatus['completing'])) {
            
            $document->setCompletedAt(new \DateTime);
            $document->setCompletedBy($currentUser->getId());
            
        }
        else{
            
            $document->setCompletedAt();
            $document->setCompletedBy();
            
        }

        $document->setActivatedAt(
                (GoodsIssueDoc::STATUS_ACTIVE == $command->statusCode && empty($document->getActivatedAt()))
                ? new \DateTime
                : $document->getActivatedAt());
        
        $em->persist($document);
        $em->flush();
        
        GoodIssueDocRegistration::registration($document, $em, $currentUser);
        
    }
    
}
