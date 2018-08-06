<?php 

namespace PricingBundle\Bus\Competitors\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\CompetitorTypeCode;
use PricingBundle\Entity\Competitor;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ThirdPartyBundle\Entity\GeoAddress;

class ToggleIsActiveCommandHandler extends MessageHandler
{
    public function handle(ToggleIsActiveCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $competitor = $em->getRepository(Competitor::class)->find($command->id);

        if (!$competitor) {
            throw new NotFoundHttpException('Конкурент не найден');
        }

        $competitor->setIsActive(boolval($command->isActive));

        $em->persist($competitor);
        $em->flush();
    }
}