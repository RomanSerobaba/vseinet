<?php 

namespace ClaimsBundle\Bus\GoodsIssue\Command;

use AppBundle\Bus\Message\MessageHandler;
use ClaimsBundle\Component\GoodsIssueComponent;
use ClaimsBundle\Entity\GoodsIssue;
use http\Exception\BadQueryStringException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SetSupplierCommandHandler extends MessageHandler
{
    public function handle(SetSupplierCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $model = $em->getRepository(GoodsIssue::class)->findOneBy(['id' => $command->id,]);
        if (!$model) {
            throw new NotFoundHttpException('Претензия не найдена');
        }

        if (!empty($model->getSupplierDecidedAt())) {
            throw new BadRequestHttpException('Невозможно изменить поставщика, если расчет с ним уже завершен');
        }

        $component = new GoodsIssueComponent($em);
        $suppliers = $component->getSuppliers();

        if (!isset($suppliers[$command->sid])) {
            throw new NotFoundHttpException('Поставщик не найден');
        }

        $model->setSupplier($command->sid);

        $em->persist($model);
        $em->flush();
    }
}