<?php 

namespace ShopBundle\Bus\Banner\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use Doctrine\ORM\Query\ResultSetMapping;
use ShopBundle\Entity\BannerMainData;
use ShopBundle\Entity\BannerMainTemplate;
use SupplyBundle\Entity\ViewSupplierOrderItem;
use ReservesBundle\Entity\Shipment;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ToggleCommandHandler extends MessageHandler
{
    public function handle(ToggleCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $model = $em->getRepository(BannerMainData::class)->find($command->id);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        $model->setIsVisible($model->getIsVisible() ? false : true);

        $em->persist($model);
        $em->flush();
    }
}