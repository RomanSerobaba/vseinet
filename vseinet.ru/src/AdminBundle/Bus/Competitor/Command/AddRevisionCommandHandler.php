<?php 

namespace AdminBundle\Bus\Competitor\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\Competitor;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductToCompetitor;
use AppBundle\Bus\Exception\ValidationException;

class AddRevisionCommandHandler extends MessageHandler
{
    public function handle(AddRevisionCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        if (null === $command->link && null === $command->competitorPrice) {
            throw new ValidationException([
                'link' => 'Укажите ссылку или цену',
                'competitorPrice' => 'Укажите ссылку или цену',
            ]);
        }

        $competitor = $em->getRepository(Competitor::class)->find($command->competitorId);
        if (!$competitor instanceof Competitor) {
            throw new NotFoundHttpException(sprintf('Конкурент %d не найден', $command->competitorId));
        }
        // @todo: check revision link by competitor link 
        $product = $em->getRepository(Product::class)->find($command->productId);
        if (!$product instanceof Product) {
            throw new NotFoundHttpException(sprintf('Товар %d не найден', $command->productId));
        }

        if ($command->id) {
            $revision = $em->getRepository(ProductToCompetitor::class)->find($command->id);
            if (!$revision instanceof ProductToCompetitor) {
                throw new NotFoundHttpException(sprintf('Товар конкурента %d не найден', $command->id));
            }
            $em->remove($revision);
            $em->flush($revision);
        }

        $revision = new ProductToCompetitor();
        $revision->setCompetitorId($competitor->getId());
        $revision->setProductId($product->getId());
        $revision->setLink($command->link);
        if ($command->competitorPrice) {
            $revision->setCompetitorPrice($command->competitorPrice);
            $revision->setPriceTime(new \DateTime());
        }
        $revision->setRequestedAt(new \DateTime());
        $revision->setCreatedBy($this->getUser()->getId());
        $revision->setCreatedAt(new \DateTime());

        $em->persist($revision);
        $em->flush();

        $command->baseProductId = $product->getBaseProductId();
    }
}
