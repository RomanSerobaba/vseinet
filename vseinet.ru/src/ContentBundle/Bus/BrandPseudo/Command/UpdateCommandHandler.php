<?php 

namespace ContentBundle\Bus\BrandPseudo\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\BrandPseudo;

class UpdateCommandHandler extends MessageHandler
{
    public function handle(UpdateCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $pseudo = $em->getRepository(BrandPseudo::class)->find($command->id);
        if (!$pseudo instanceof BrandPseudo) {
            throw new NotFoundHttpException(sprintf('Псевдноним бренда %d не найден', $command->id));
        }

        $pseudo->setName($command->name);

        $em->persist($pseudo);
        $em->flush();
    }
}