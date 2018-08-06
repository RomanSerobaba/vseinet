<?php 

namespace ClaimsBundle\Bus\GoodsIssue\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use ClaimsBundle\Component\GoodsIssueComponent;
use ClaimsBundle\Entity\GoodsIssue;
use ClaimsBundle\Entity\GoodsIssueType;
use http\Exception\BadQueryStringException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TypeCommandHandler extends MessageHandler
{
    public function handle(TypeCommand $command)
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
            throw new BadRequestHttpException('Вы не можете изменить тип претензии после принятия решения по товару');
        }
        if (!empty($model->getOrderItemId()) && $command->type === GoodsIssueType::CODE_FOUND) {
            throw new BadRequestHttpException('Вы не можете изменить тип у клиентской претензии на указанный');
        }

        $component = new GoodsIssueComponent($em);
        $types = $component->getGoodIssuesTypes();
        if (!isset($types[$command->type])) {
            throw new BadRequestHttpException('Неверный тип претензии: '.$command->type);
        }

        $model->setGoodsIssueTypeCode($command->type);

        $em->persist($model);
        $em->flush();
    }
}