<?php

namespace ClaimsBundle\Bus\GoodsIssue\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use ClaimsBundle\Entity\GoodsIssue;
use OrderBundle\Entity\OrderItem;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SetClientDecisionCommandHandler extends MessageHandler
{
    public function handle(SetClientDecisionCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $model = $em->getRepository(GoodsIssue::class)->findOneBy(['id' => $command->id,]);
        if (!$model) {
            throw new NotFoundHttpException('Претензия не найдена');
        }

        if (!in_array($command->decision, [
            GoodsIssue::CLIENT_DECISION_RETURNED_MONEY,
            GoodsIssue::CLIENT_DECISION_RETURNED_GOODS,
        ])
        ) {
            throw new BadRequestHttpException('Выбрано не существующее решение по клиенту');
        }

        if (!empty($model->getClientDecidedAt())) {
            throw new BadRequestHttpException('Расчет по клиенту уже был завершен до этого');
        }

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();

        $model->setClientDecidedAt(new \DateTime());
        $model->setClientDecidedBy($currentUser->getId());
        $model->setClientDecision($command->decision);
        $model->setClientDecisionComment(trim($command->comment));
        $model->setClientPenalty(intval($command->forfeit));

        $em->persist($model);
        $em->flush();
    }
}