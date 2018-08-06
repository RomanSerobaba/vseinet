<?php 

namespace MatrixBundle\Bus\Representative\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use OrgBundle\Entity\Representative;
use MatrixBundle\Entity\TradeMatrixTemplate;
use MatrixBundle\Entity\TradeMatrixTemplateToRepresentative;

class UnlinkCommandHandler extends MessageHandler
{
    public function handle(UnlinkCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $representative = $em->getRepository(Representative::class)->find($command->id);
        if (!$representative instanceof Representative) {
            throw new NotFoundHttpException(sprintf('Точка %d не найдена', $command->id));
        }

        $template = $em->getRepository(TradeMatrixTemplate::class)->find($command->templateId);
        if (!$template instanceof TradeMatrixTemplate) {
            throw new NotFoundHttpException(sprintf('Шаблон %d не найден', $command->templateId));
        }
        
        $linker = $em->getRepository(TradeMatrixTemplateToRepresentative::class)->findOneBy(['representativeId' => $command->id, 'tradeMatrixTemplateId' => $command->templateId]);
        if ($linker instanceof TradeMatrixTemplateToRepresentative) {
            $em->remove($linker);
            $em->flush();   
        }
    }
}