<?php 

namespace ClaimsBundle\Bus\GoodsIssue\Command;

use AppBundle\Bus\Message\MessageHandler;
use ClaimsBundle\Entity\GoodsIssue;
use http\Exception\BadQueryStringException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SetCompensationCommandHandler extends MessageHandler
{
    public function handle(SetCompensationCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $text = trim($command->value);
        if (empty($text)) {
            throw new BadQueryStringException('Не указана проблема');
        }

        $model = $em->getRepository(GoodsIssue::class)->findOneBy(['id' => $command->id,]);
        if (!$model) {
            throw new NotFoundHttpException('Претензия не найдена');
        }

        if (!empty($model->getSupplierDecidedAt())) {
            throw new BadRequestHttpException('Невозможно изменить сумму компенсации, если расчет с поставщиком уже завершен');
        }

        $value = \ServiceBundle\Components\Number::input($command->value);

        $model->setSupplierCompensation($value);

        $em->persist($model);
        $em->flush();
    }
}