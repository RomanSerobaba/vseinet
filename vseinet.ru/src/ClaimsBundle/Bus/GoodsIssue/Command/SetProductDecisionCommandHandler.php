<?php

namespace ClaimsBundle\Bus\GoodsIssue\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use ClaimsBundle\Entity\GoodsIssue;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SetProductDecisionCommandHandler extends MessageHandler
{
    public function handle(SetProductDecisionCommand $command)
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
            GoodsIssue::GOODS_DECISION_REMOVED_FROM_BALANCE,
            GoodsIssue::GOODS_DECISION_RETURNED_ON_BALANCE,
            GoodsIssue::GOODS_DECISION_RETURNED_TO_CLIENT,
        ])
        ) {
            throw new BadRequestHttpException('Выбрано не существующее решение по товару');
        }

        if (!empty($model->getGoodsDecidedAt())) {
            throw new BadRequestHttpException('Расчет по товару уже был завершен до этого');
        }

        if ($command->decision === GoodsIssue::GOODS_DECISION_RETURNED_TO_CLIENT
            && !empty($model->getClientDecidedAt())
            && $model->getClientDecision() === GoodsIssue::CLIENT_DECISION_RETURNED_MONEY
        ) {
            throw new BadRequestHttpException('Вы не можете вернуть товар клиенту, если ему уже были возвращены деньги');
        }

        if ($command->decision === GoodsIssue::GOODS_DECISION_RETURNED_TO_CLIENT && empty($model->getOrderItemId())) {
            throw new BadRequestHttpException('Вы не можете вернуть не заказанный товар клиенту');
        }

        if ($command->decision === GoodsIssue::GOODS_DECISION_RETURNED_ON_BALANCE && empty($model->getGeoRoomId())) {
            throw new BadRequestHttpException('Вы не указали помещение для зачисления товара из претензии');
        }

        if ($command->decision !== GoodsIssue::GOODS_DECISION_RETURNED_TO_CLIENT
            && !empty($model->getClientDecidedAt())
            && $model->getClientDecision() === GoodsIssue::CLIENT_DECISION_RETURNED_GOODS
        ) {
            throw new BadRequestHttpException('Вы не можете списать или принять на склад товар уже возвращенный клиенту');
        }

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();

        $model->setGoodsDecidedAt($command->decision === GoodsIssue::GOODS_DECISION_RETURNED_ON_BALANCE  ? null : new \DateTime());
        $model->setGoodsDecidedBy($currentUser->getId());
        $model->setGoodsDecision($command->decision);
        $model->setGoodsDecisionComment(trim($command->comment));

        $em->persist($model);
        $em->flush();
    }
}