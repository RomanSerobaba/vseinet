<?php 

namespace MatrixBundle\Bus\Template\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use MatrixBundle\Entity\TradeMatrixTemplate;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $template = $em->getRepository(TradeMatrixTemplate::class)->findOneBy(['name' => $command->name]);
        if ($template instanceof TradeMatrixTemplate) {
            throw new BadRequestHttpException(sprintf('Шаблон с названием %s уже существует', $command->name));  
        }

        if ($command->isSeasonal && (null === $command->activeFrom || null === $command->activeTill)) {
            throw new BadRequestHttpException(sprintf('У сезонного шаблона должен быть указан период действия'));  
        }

        $template = new TradeMatrixTemplate();
        $template->setName($command->name);

        if ($command->isSeasonal) {
            $template->setActiveFrom($command->activeFrom);
            $template->setActiveTill($command->activeTill);
        }

        $em->persist($template);
        $em->flush();

        $this->get('uuid.manager')->saveId($command->uuid, $template->getId());
    }
}