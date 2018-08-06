<?php 

namespace ContentBundle\Bus\Brand\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Brand;
use ContentBundle\Entity\BrandPseudo;

class GetPseudosQueryHandler extends MessageHandler
{
    public function handle(GetPseudosQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $brand = $em->getRepository(Brand::class)->find($query->id);
        if (!$brand instanceof Brand) {
            throw new NotFoundHttpException(sprintf('Бренд %s не найден', $query->id));
        }

        return $em->getRepository(BrandPseudo::class)->findBy(['brandId' => $brand->getId()], ['name' => 'ASC']);
    }
}