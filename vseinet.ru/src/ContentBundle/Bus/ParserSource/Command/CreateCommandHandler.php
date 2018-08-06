<?php 

namespace ContentBundle\Bus\ParserSource\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\ParserSource;
use SupplyBundle\Entity\Supplier;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        if ($command->supplierId) {
            $supplier = $em->getRepository(Supplier::class)->find($command->supplierId);
            if (!$supplier instanceof Supplier) {
                throw new NotFoundHttpException(sprintf('Поставщик %s не найден', $command->supplierId));
            }
            if (!$command->code) {
                $command->code = $supplier->getCode();
            }
            if (!$command->alias) {
                $command->alias = $supplier->getCode();
            }
        }

        $source = new ParserSource();
        $source->setCode($command->code);
        $source->setAlias($command->alias);
        $source->setSupplierId($command->supplierId);
        $source->setUrl($command->url);
        $source->setUseAntiGuard($command->useAntiGuard);
        $source->setIsParseImages($command->isParseImages);
        $source->setIsActive(true);
        $source->setSortOrder($this->getMaxSortOrder() + 1);

        $em->persist($source);
        $em->flush();

        $this->get('uuid.manager')->saveId($command->uuid, $source->getId());
    }

    protected function getMaxSortOrder()
    {
        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT MAX(ps.sortOrder)
            FROM ContentBundle:ParserSource ps 
        ");

        try {
            return $q->getSingleScalarResult();
        } 
        catch (NoResultException $e) {
            return 0;
        }
    }
}