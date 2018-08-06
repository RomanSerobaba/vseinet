<?php 

namespace ShopBundle\Bus\Banner\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use Doctrine\ORM\Query\ResultSetMapping;
use ShopBundle\Entity\BannerMainData;
use ShopBundle\Entity\BannerMainTemplate;
use SupplyBundle\Entity\ViewSupplierOrderItem;
use ReservesBundle\Entity\Shipment;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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

        $model = new BannerMainData();
        $model->setTitle($command->title);
        $model->setType($command->type);
        $model->setWeight($command->weight);
        $model->setUrl($command->url);
        $model->setTabBackgroundColor($command->tabBackgroundColor);
        $model->setTabTextColor($command->tabTextColor);
        $model->setTabIsFixed($command->tabIsFixed);
        $model->setBackgroundColor($command->backgroundColor);
        $model->setTemplatesId($command->templatesId);
        $model->setIsVisible($command->isVisible);
        $model->setStartVisibleDate($command->startVisibleDate);
        $model->setEndVisibleDate($command->endVisibleDate);
        $model->setTabImg($command->tabImg);
        $model->setTitleTextColor($command->titleTextColor);
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