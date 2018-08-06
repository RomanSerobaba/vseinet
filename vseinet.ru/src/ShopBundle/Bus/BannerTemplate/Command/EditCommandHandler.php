<?php 

namespace ShopBundle\Bus\BannerTemplate\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use Doctrine\ORM\Query\ResultSetMapping;
use ShopBundle\Entity\BannerMainTemplate;
use SupplyBundle\Entity\ViewSupplierOrderItem;
use ReservesBundle\Entity\Shipment;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EditCommandHandler extends MessageHandler
{
    public function handle(EditCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $model = $em->getRepository(BannerMainTemplate::class)->find($command->id);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        $model->setName($command->name);

        $model->setImgBackgroundPc($command->imgBackgroundPc);
        $model->setImgBackgroundTablet($command->imgBackgroundTablet);
        $model->setImgBackgroundPhone($command->imgBackgroundPhone);
        $model->setPosBackgroundPcX($command->posBackgroundPcX);
        $model->setPosBackgroundPcY($command->posBackgroundPcY);
        $model->setPosBackgroundTabletX($command->posBackgroundTabletX);
        $model->setPosBackgroundTabletY($command->posBackgroundTabletY);
        $model->setPosBackgroundPhoneX($command->posBackgroundPhoneX);
        $model->setPosBackgroundPhoneY($command->posBackgroundPhoneY);

        $em->persist($model);
        $em->flush();
    }
}