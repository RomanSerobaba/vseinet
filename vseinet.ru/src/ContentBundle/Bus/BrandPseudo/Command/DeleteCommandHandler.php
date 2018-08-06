<?php 

namespace ContentBundle\Bus\BrandPseudo\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\BrandPseudo;

class DeleteCommandHandler extends MessageHandler
{
    public function handle(DeleteCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $pseudo = $em->getRepository(BrandPseudo::class)->find($command->id);
        if (!$pseudo instanceof BrandPseudo) {
            throw new NotFoundHttpException(sprintf('Псевдоним бренда %s не найден', $command->id));
        }

        $em->remove($pseudo);
        $em->flush();
    }
}