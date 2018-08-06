<?php 

namespace OrgBundle\Bus\Suggestions\Command;

use AppBundle\Bus\Message\MessageHandler;
use OrgBundle\Entity\ClientSuggestion;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CheckSuggestionsCommandHandler extends MessageHandler
{
    public function handle(CheckSuggestionsCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var ClientSuggestion $suggestion
         */
        $suggestion = $em->getRepository(ClientSuggestion::class)->find($command->id);

        if (!$suggestion) {
            throw new NotFoundHttpException('Предложение не найдено');
        }

        if ($suggestion->getIsChecked() != boolval($command->isCheck)) {
            $suggestion->setIsChecked(boolval($command->isCheck));

            $em->persist($suggestion);
            $em->flush();
        }
    }
}