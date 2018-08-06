<?php 

namespace ContentBundle\Bus\BrandPseudo\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ContentBundle\Entity\Brand;
use ContentBundle\Entity\BrandPseudo;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $brand = $em->getRepository(Brand::class)->find($command->brandId);
        if (!$brand instanceof Brand) {
            throw new NotFoundHttpException(sprintf('Бренд %d не найден', $command->brandId));
        }

        $pseudo = new BrandPseudo();
        $pseudo->setBrandId($brand->getId());
        $pseudo->setName($command->name);

        $em->persist($pseudo);
        $em->flush();

        $this->get('uuid.manager')->saveId($command->uuid, $pseudo->getId());
    }
}