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

class EditCommandHandler extends MessageHandler
{
    public function handle(EditCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $model = $em->getRepository(BannerMainData::class)->find($command->id);

        if (!$model) {
            throw new NotFoundHttpException(sprintf('Баннер %d не найден', $command->id));
        }

        $model->setTitle($command->title);
        $model->setType($command->type);
        $model->setWeight($command->weight);
        $model->setUrl($command->url);
        $model->setTabBackgroundColor($command->tabBackgroundColor);
        $model->setTabText($command->tabText);
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

        $model->setImgBackgroundPc($command->imgBackgroundPc);
        $model->setImgBackgroundTablet($command->imgBackgroundTablet);
        $model->setImgBackgroundPhone($command->imgBackgroundPhone);

        $model->setDescriptionPc($command->descriptionPc);
        $model->setDescriptionTablet($command->descriptionTablet);
        $model->setDescriptionPhone($command->descriptionPhone);

        $model->setTitlePc($command->titlePc);
        $model->setTitleTablet($command->titleTablet);
        $model->setTitlePhone($command->titlePhone);

        $model->setTextUrlPc($command->textUrlPc);
        $model->setTextUrlTablet($command->textUrlTablet);
        $model->setTextUrlPhone($command->textUrlPhone);
        
        $model->setLeftDetailsPc($command->leftDetailsPc);
        $model->setLeftDetailsTablet($command->leftDetailsTablet);
        $model->setLeftDetailsPhone($command->leftDetailsPhone);
        
        $model->setRightDetailsPc($command->rightDetailsPc);
        $model->setRightDetailsTablet($command->rightDetailsTablet);
        $model->setRightDetailsPhone($command->rightDetailsPhone);

        $em->persist($model);
        $em->flush();
    }
}