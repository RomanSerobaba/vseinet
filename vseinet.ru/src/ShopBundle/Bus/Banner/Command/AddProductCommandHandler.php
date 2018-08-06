<?php 

namespace ShopBundle\Bus\Banner\Command;

use AppBundle\Bus\Message\MessageHandler;
use ShopBundle\Entity\BannerMainProductData;

class AddProductCommandHandler extends MessageHandler
{
    public function handle(AddProductCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $model = new BannerMainProductData();
        $model->setBannerId($command->bannerId);
        $model->setBaseProductId($command->baseProductId);
        $model->setTitle($command->title);
        $model->setPhotoPc($command->photoPc);
        $model->setPhotoTablet($command->photoTablet);
        $model->setPhotoPhone($command->photoPhone);
        $model->setTitlePc($command->titlePc);
        $model->setTitleTablet($command->titleTablet);
        $model->setTitlePhone($command->titlePhone);
        $model->setPrice($command->price);
        $model->setSalePrice($command->salePrice);

        $em->persist($model);
        $em->flush();
    }
}