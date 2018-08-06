<?php 

namespace SiteBundle\Bus\Product\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use PricingBundle\Entity\Product;

class IncreaseRatingCommandHandler extends MessageHandler
{
    public function handle(IncreaseRatingCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository(Product::class)->findOneBy(['baseProductId' => $command->baseProductId]);
        if (!$product instanceof Product) {
            throw new NotFoundHttpException(sprintf('Товар с кодом %d не найден', $command->baseProductId));
        }

        $product->setRating($product->getRating() + 1);

        $em->persist($product);
        $em->flush();
    }
}