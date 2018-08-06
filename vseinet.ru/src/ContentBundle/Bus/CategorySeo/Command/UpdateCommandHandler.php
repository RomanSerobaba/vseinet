<?php 

namespace ContentBundle\Bus\CategorySeo\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\CategorySeo;
use ContentBundle\Entity\Brand;

class UpdateCommandHandler extends MessageHandler
{
    public function handle(UpdateCommand $command)
    {
        $em = $this->getDoctrine()->getManager();
        
        $seo = $em->getRepository(CategorySeo::class)->find($command->id);
        if (!$seo instanceof CategorySeo) {
            throw new NotFoundHttpException(sprintf('SEO %d категории не найдено', $command->id));
        }

        if ($command->brandId) {
            $brand = $em->getRepository(Brand::class)->find($command->brandId);
            if (!$brand instanceof Brand) {
                throw new NotFoundHttpException(sprintf('Бренд %d не найден', $command->brandId));
            }
        }

        $seo->setBrandId($command->brandId);
        $seo->setDescription($command->description);
        $seo->setPageTitle($command->pageTitle);
        $seo->setPageDescription($command->pageDescription);

        $em->persist($seo);
        $em->flush();
    }
}