<?php 

namespace MatrixBundle\Bus\Template\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use MatrixBundle\Entity\TradeMatrixTemplate;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UpdateCommandHandler extends MessageHandler
{
    public function handle(UpdateCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $template = $em->getRepository(TradeMatrixTemplate::class)->find($command->id);
        if (!$template instanceof TradeMatrixTemplate) {
            throw new NotFoundHttpException(sprintf('Шаблон %d не найден', $command->id));
        }

        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT t.id
            FROM MatrixBundle:TradeMatrixTemplate AS t
            WHERE t.name = :name AND t.id != :id
        ");
        $q->setParameter('id', $command->id);
        $q->setParameter('name', $command->name);

        if ($q->getOneOrNullResult() instanceof TradeMatrixTemplate) {
            throw new BadRequestHttpException(sprintf('Шаблон с названием %s уже существует', $command->name));  
        }

        if ($command->isSeasonal && (null === $command->activeFrom || null === $command->activeTill)) {
            throw new BadRequestHttpException(sprintf('У сезонного шаблона должен быть указан период действия'));  
        }

        $template->setName($command->name);

        if ($command->isSeasonal) {
            $template->setActiveFrom($command->activeFrom);
            $template->setActiveTill($command->activeTill);
        } else {
            $template->setActiveFrom(null);
            $template->setActiveTill(null);
        }

        $em->persist($template);
    }
}