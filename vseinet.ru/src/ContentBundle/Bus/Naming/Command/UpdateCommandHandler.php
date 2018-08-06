<?php 

namespace ContentBundle\Bus\Naming\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\BaseProductNaming;

class UpdateCommandHandler extends MessageHandler
{
    public function handle(UpdateCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $naming = $em->getRepository(BaseProductNaming::class)->find($command->id);
        if (!$naming instanceof BaseProductNaming) {
            throw new NotFoundHttpException(sprintf('Элемент наименования %d не найден.', $command->id));
        }

        $naming->setDelimiterBefore($command->delimiterBefore);
        $naming->setDelimiterAfter($command->delimiterAfter);
        $naming->setIsRequired($command->isRequired);

        $em->persist($naming);
        $em->flush();
    }
}