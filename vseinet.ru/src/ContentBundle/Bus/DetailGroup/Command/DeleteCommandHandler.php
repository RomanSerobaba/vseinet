<?php 

namespace ContentBundle\Bus\DetailGroup\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ContentBundle\Entity\DetailGroup;
use ContentBundle\Entity\Detail;

class DeleteCommandHandler extends MessageHandler
{
    public function handle(DeleteCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $group = $em->getRepository(DetailGroup::class)->find($command->id);
        if (!$group instanceof DetailGroup) {
            throw new NotFoundHttpException(sprintf('Группа характеристик %d  не найдена', $command->id));
        }

        $detail = $em->getRepository(Detail::class)->findOneBy(['groupId' => $group->getId()]);
        if ($detail instanceof Detail) {
            throw new BadRequestHttpException('Удаление группы невозможно, в группе находятся характеристики');
        }

        $em->remove($group);
        $em->flush();
    }
}