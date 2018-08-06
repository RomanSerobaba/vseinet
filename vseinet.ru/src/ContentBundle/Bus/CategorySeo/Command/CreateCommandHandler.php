<?php 

namespace ContentBundle\Bus\CategorySeo\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Category;
use ContentBundle\Entity\CategorySeo;
use ContentBundle\Entity\Brand;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command)
    {
        $em = $this->getDoctrine()->getManager();
        
        $category = $em->getRepository(Category::class)->find($command->categoryId);
        if (!$category instanceof Category) {
            throw new NotFoundHttpException(sprintf('Категория %d не найдена', $command->categoryId));
        }

        if ($command->brandId) {
            $brand = $em->getRepository(Brand::class)->find($command->brandId);
            if (!$brand instanceof Brand) {
                throw new NotFoundHttpException(sprintf('Бренд %d не найден', $command->brandId));
            }
        }

        $seo = new CategorySeo();
        $seo->setCategoryId($command->categoryId);
        $seo->setBrandId($command->brandId);
        $seo->setDescription($command->description);
        $seo->setPageTitle($command->pageTitle);
        $seo->setPageDescription($command->pageDescription);

        $em->persist($seo);
        $em->flush();

        $this->get('uuid.manager')->saveId($command->uuid, $seo->getId());
    }
}