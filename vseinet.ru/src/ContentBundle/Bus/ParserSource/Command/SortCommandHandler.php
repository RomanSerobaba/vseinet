<?php 

namespace ContentBundle\Bus\ParserSource\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\ParserSource;

/**
 * @deprecated
 */
class SortCommandHandler extends MessageHandler
{
    public function handle(SortCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $source = $em->getRepository(ParserSource::class)->find($command->id);
        if (!$source instanceof ParserSource) {
            throw new NotFoundHttpException(sprintf('Источник парсинга %s  не найден', $command->id));
        }

        if (0 === $command->underId) {
            $sortOrder = 0;
        } 
        else {
            $under = $em->getRepository(ParserSource::class)->find($command->underId);
            if (!$under instanceof ParserSource) {
                throw new NotFoundHttpException(sprintf('Источник парсинга %s  не найден', $command->underId));
            }
            $sortOrder = $under->getSortOrder();
        }

        $this->incSortOrderBelow($sortOrder);

        $source->setSortOrder($sortOrder + 1);

        $em->persist($source);
        $em->flush();
    }

    protected function incSortOrderBelow(int $sortOrder)
    {
        $query = $this->getDoctrine()->getManager()->createQuery("
            UPDATE ContentBundle:ParserSource ps
            SET ps.sortOrder = ps.sortOrder + 1
            WHERE ps.sortOrder > :sortOrder
        ");
        $query->setParameter('sortOrder', $sortOrder);
        $query->execute();
    }
}