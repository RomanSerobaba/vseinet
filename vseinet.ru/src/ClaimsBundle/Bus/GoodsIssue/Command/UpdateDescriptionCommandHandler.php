<?php 

namespace ClaimsBundle\Bus\GoodsIssue\Command;

use AppBundle\Bus\Message\MessageHandler;
use ClaimsBundle\Entity\GoodsIssue;
use http\Exception\BadQueryStringException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpdateDescriptionCommandHandler extends MessageHandler
{
    public function handle(UpdateDescriptionCommand $command)
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

        $model->setDescription($text);

        $em->persist($model);
        $em->flush();
    }
}