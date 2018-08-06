<?php 

namespace ContentBundle\Bus\Brand\Command; 

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Brand;

class UpdateCommandHandler extends MessageHandler
{
    public function handle(UpdateCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $brand = $em->getRepository(Brand::class)->find($command->id);
        if (!$brand instanceof Brand) {
            throw new NotFoundHttpException(sprintf('Бренд %s не найден', $command->id));
        } 

        $brand->setName($command->name);
        $brand->setUrl($command->url);
        
        $em->persist($brand);
        $em->flush();
    }
}