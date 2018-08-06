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

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $model = new BannerMainTemplate();
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

        $this->get('uuid.manager')->saveId($command->uuid, $model->getId());
    }
}