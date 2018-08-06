<?php 

namespace ClaimsBundle\Bus\GoodsIssue\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use ClaimsBundle\Entity\GoodsIssue;
use http\Exception\BadQueryStringException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AcceptedCommandHandler extends MessageHandler
{
    public function handle(AcceptedCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $model = $em->getRepository(GoodsIssue::class)->findOneBy(['id' => $command->id,]);
        if (!$model) {
            throw new NotFoundHttpException('Претензия не найдена');
        }

        if ($command->on) {
            /**
             * @var User $currentUser
             */
            $currentUser = $this->get('user.identity')->getUser();

            $model->setAcceptedAt(new \DateTime());
            $model->setAcceptedBy($currentUser->getId());
        } else {
            $model->setAcceptedAt(null);
            $model->setAcceptedBy(null);
        }

        $em->persist($model);
        $em->flush();
    }
}