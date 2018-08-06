<?php 

namespace ContentBundle\Bus\BaseProduct\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ContentBundle\Entity\BaseProduct;

class DeleteCommandHandler extends MessageHandler
{
    public function handle(DeleteCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $products = $em->getRepository(BaseProduct::class)->findBy(['id' => $command->ids]);
        if (empty($products)) {
            throw new BadRequestHttpException('Выберите товары для удаления');
        }

        $q = $em->createQuery("
            SELECT bp.id
            FROM ContentBundle:BaseProduct bp 
            LEFT OUTER JOIN OrderBundle:OrderItem oi WITH oi.baseProductId = bp.id 
            LEFT OUTER JOIN SupplyBundle:SupplyItem si WITH si.baseProductId = bp.id
            WHERE bp.id IN (:ids) AND (oi.id IS NOT NULL OR si.id IS NOT NULL) 
            GROUP BY bp.id
        ");
        $q->setParameter('ids', $command->ids);
        $imposibleDeleteIds = $q->getResult('ListHydrator');
        if (!empty($imposibleDeleteIds)) {
            $command->ids = array_diff($command->ids, $imposibleDeleteIds);
            $this->get('command_bus')->handle(new SetIsHiddenCommand(['ids' => $imposibleDeleteIds, 'value' => true]));
        }

        if (!empty($command->ids)) {
            $products = $em->getRepository(BaseProduct::class)->findBy(['id' => $command->ids]);
            foreach ($products as $product) {
                $em->remove($product);
            }
            $em->flush();
        }
    }
}