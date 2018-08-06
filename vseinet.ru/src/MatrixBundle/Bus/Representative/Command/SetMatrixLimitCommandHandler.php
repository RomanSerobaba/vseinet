<?php 

namespace MatrixBundle\Bus\Representative\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use OrgBundle\Entity\Representative;
use MatrixBundle\Entity\TradeMatrixLimit;

class SetMatrixLimitCommandHandler extends MessageHandler
{
    public function handle(SetMatrixLimitCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $representative = $em->getRepository(Representative::class)->findOneBy(['geoPointId' => $command->id]);
        if (!$representative instanceof Representative) {
            throw new NotFoundHttpException(sprintf('Точка %d не найдена', $command->id));
        }

        $limit = $em->getRepository(TradeMatrixLimit::class)->findOneBy(['representativeId' => $command->id]);
        if (!$limit instanceof TradeMatrixLimit) {
            $limit = new TradeMatrixLimit();
            $limit->setRepresentativeId($command->id);
        }
        
        $limit->setLimitAmount($command->value);

        $em->persist($limit);
        $em->flush();
    }
}