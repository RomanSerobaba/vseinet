<?php 

namespace ContentBundle\Bus\BaseProduct\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\BaseProduct;

class GetDetailValuesQueryHandler extends MessageHandler
{
    public function handle(GetDetailValuesQuery $query)
    {
        $product = $this->getDoctrine()->getManager()->getRepository(BaseProduct::class)->findOneBy(['id' => $query->id]);
        if (!$product instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар %d не найден', $query->id));
        }

        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT 
                NEW ContentBundle\Bus\BaseProduct\Query\DTO\DetailValue (
                    dtp.detailId,
                    d.typeCode,
                    dtp.value,
                    dtp.valueId 
                )
            FROM ContentBundle:DetailToProduct dtp
            INNER JOIN ContentBundle:Detail d WITH d.id = dtp.detailId
            WHERE dtp.baseProductId = :baseProductId 
        ");
        $q->setParameter('baseProductId', $product->getId());
        $values = $q->getArrayResult();

        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT 
                NEW ContentBundle\Bus\BaseProduct\Query\DTO\DetailValue (
                    dmtp.detailId,
                    d.typeCode,
                    dmtp.memo 
                )
            FROM ContentBundle:DetailMemoToProduct dmtp
            INNER JOIN ContentBundle:Detail d WITH d.id = dmtp.detailId
            WHERE dmtp.baseProductId = :baseProductId 
        ");
        $q->setParameter('baseProductId', $product->getId());
        $memos = $q->getArrayResult();    

        return array_merge($values, $memos);
    }
}
