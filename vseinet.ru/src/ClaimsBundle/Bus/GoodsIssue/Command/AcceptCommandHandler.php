<?php 

namespace ClaimsBundle\Bus\GoodsIssue\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use ClaimsBundle\Entity\GoodsIssue;
use ClaimsBundle\Entity\GoodsIssueType;
use http\Exception\BadQueryStringException;
use OrderBundle\Entity\OrderItem;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AcceptCommandHandler extends MessageHandler
{
    public function handle(AcceptCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $model = $em->getRepository(GoodsIssue::class)->findOneBy(['id' => $command->id,]);
        if (!$model) {
            throw new NotFoundHttpException('Претензия не найдена');
        }

        if (!empty($model->getGoodsDecidedAt())) {
            throw new BadRequestHttpException('Расчет по товару уже был завершен до этого');
        }
        if ($model->getGoodsDecision() !== GoodsIssue::GOODS_DECISION_RETURNED_ON_BALANCE) {
            throw new BadRequestHttpException('Было принято решение не возвращать товар на баланс');
        }
//        if (!isset($this->user->accountable_for[$reclamation['destination_room_id']]) && !$this->user->isGranted('ANY_POINT_RECLAMATION_PRODUCT_APPROVAL')) {
//            throw new BadRequestHttpException("У вас нет прав для подтверждения");
//        }
        if (!empty($model->getClientDecidedAt()) && $model->getClientDecision() === GoodsIssue::CLIENT_DECISION_RETURNED_GOODS) {
            throw new BadRequestHttpException("Нельзя одновременно вернуть товар клиенту и на баланс");
        }

        $orderItem = $em->getRepository(OrderItem::class)->findOneBy(['id' => ($model->getProductResortId() ?: $model->getOrderItemId())]);

        if ($model->getGoodsIssueTypeCode() === GoodsIssueType::CODE_REGRADING && empty($orderItem)) {
            throw new BadRequestHttpException("Товара с таким кодом для зачисления пересорта не существует");
        }

        $model->setGoodsDecidedAt(new \DateTime());

        $em->persist($model);
        $em->flush();
    }
}