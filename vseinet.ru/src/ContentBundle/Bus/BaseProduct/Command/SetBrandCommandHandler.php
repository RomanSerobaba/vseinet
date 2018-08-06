<?php 

namespace ContentBundle\Bus\BaseProduct\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ContentBundle\Entity\BaseProduct;
use ContentBundle\Entity\Brand;
use ContentBundle\Entity\BaseProductEditLog;
use AppBundle\Enum\BaseProductEditTarget;

class SetBrandCommandHandler extends MessageHandler
{
    public function handle(SetBrandCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository(BaseProduct::class)->find($command->id);
        if (!$product instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар %d не найден', $command->id));
        }

        $brand = $em->getRepository(Brand::class)->findOneBy(['name' => $command->name]);
        if (!$brand instanceof Brand) {
            $brand = new Brand();
        }
        elseif ($brand->getIsForbidden()) {
            throw new BadRequestHttpException(sprintf('Бренд "%s" запрещен к показу на сайте', $brand->getName()));
        }
        
        $brand->setName($command->name);
        $brand->setUrl($command->url);
        $brand->setIsForbidden(false);
        $em->persist($brand);

        $em->getRepository(BaseProductEditLog::class)->add(
            $product,
            BaseProductEditTarget::BRAND,
            null, 
            $this->get('user.identity')->getUser(),
            $product->getBrandId(),
            $brand->getId()
        );

        $product->setBrandId($brand->getId());
        $em->persist($product);
        $em->flush();
    }
}