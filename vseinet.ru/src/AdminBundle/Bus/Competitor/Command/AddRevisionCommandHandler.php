<?php

namespace AdminBundle\Bus\Competitor\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\Competitor;
use AppBundle\Entity\Product;
use AppBundle\Entity\CompetitorProduct;
use AppBundle\Exception\ValidationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AddRevisionCommandHandler extends MessageHandler
{
    public function handle(AddRevisionCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        if (null === $command->url && null === $command->price) {
            throw new ValidationException('url', 'Укажите ссылку или цену');
        }

        $competitor = $em->getRepository(Competitor::class)->find($command->competitorId);
        if (!$competitor instanceof Competitor) {
            throw new NotFoundHttpException(sprintf('Конкурент %d не найден', $command->competitorId));
        }

        // @todo: check revision link by competitor link
        $product = $em->getRepository(Product::class)->findOneBy([
            'baseProductId' => $command->baseProductId,
        ], [
            'geoCityId' => 'DESC',
        ]);
        if (!$product instanceof Product) {
            throw new NotFoundHttpException(sprintf('Товар %d не найден', $command->baseProductId));
        }

        if ($command->id) {
            $revision = $em->getRepository(CompetitorProduct::class)->find($command->id);
            if (!$revision instanceof CompetitorProduct) {
                throw new NotFoundHttpException(sprintf('Товар конкурента %d не найден', $command->id));
            }
            $em->remove($revision);
            $em->flush($revision);
        }

        if ($command->url && $em->getRepository(CompetitorProduct::class)->findOneBy(['url' => $command->url, 'competitorId' => $competitor->getId()])) {
            throw new BadRequestHttpException(sprintf('Такая ссылка на товар конкурента уже есть в базе'));
        }

        if ($command->url && $em->getRepository(CompetitorProduct::class)->findOneBy(['baseProductId' => $product->getBaseProductId(), 'competitorId' => $competitor->getId()])) {
            throw new BadRequestHttpException(sprintf('Такой товар конкурента уже есть в базе'));
        }

        $revision = new CompetitorProduct();
        $revision->setCompetitorId($competitor->getId());
        $revision->setBaseProductId($product->getBaseProductId());
        $revision->setGeoCityId(0);
        $revision->setUrl($command->url);
        if ($command->price && 'retail' === $competitor->getChannel()) {
            $revision->setPrice($command->price);
            $revision->setCompletedAt(new \DateTime());
        } elseif ($command->url) {
            $revision->setIsManualRequest(true);
        }
        $revision->setCreatedBy($this->getUser()->getId());

        $em->persist($revision);
        $em->flush();
    }
}
