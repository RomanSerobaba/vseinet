<?php 

namespace OrgBundle\Bus\Suggestions\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use OrgBundle\Entity\ClientSuggestion;
use OrgBundle\Entity\ClientSuggestionComment;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AddCommentCommandHandler extends MessageHandler
{
    public function handle(AddCommentCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();

        /**
         * @var ClientSuggestion $suggestion
         */
        $suggestion = $em->getRepository(ClientSuggestion::class)->find($command->id);

        if (!$suggestion) {
            throw new NotFoundHttpException('Предложение не найдено');
        }

        $model = new ClientSuggestionComment();
        $model->setText($command->text);
        $model->setClientSuggestionId($suggestion->getId());
        $model->setCreatedAt(new \DateTime());
        $model->setCreatedBy(6/*$currentUser->getId()*/);

        $em->persist($model);
        $em->flush();

        $this->get('uuid.manager')->saveId($command->uuid, $model->getId());
    }
}