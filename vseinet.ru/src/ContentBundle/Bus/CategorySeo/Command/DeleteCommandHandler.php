<?php 

namespace ContentBundle\Bus\CategorySeo\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\CategorySeo;

class DeleteCommandHandler extends MessageHandler
{
    public function handle(DeleteCommand $command)
    {
        $em = $this->getDoctrine()->getManager();
        
        $seo = $em->getRepository(CategorySeo::class)->find($command->id);
        if (!$seo instanceof CategorySeo) {
            throw new NotFoundHttpException(sprintf('Seo %d для категории не найдено', $command->id));
        }

        $em->remove($seo);
        $em->flush();
    }
}