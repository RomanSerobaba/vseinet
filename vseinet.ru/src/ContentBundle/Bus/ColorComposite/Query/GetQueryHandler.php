<?php 

namespace ContentBundle\Bus\ColorComposite\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Color;
use ContentBundle\Entity\ColorComposite;

class GetQueryHandler extends MessageHandler
{
    public function handle(GetQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $composite = $em->getRepository(ColorComposite::class)->find($query->id);
        if (!$composite instanceof ColorComposite) {
            throw new NotFoundHttpException(sprintf('Составной цвет %d не найден', $query->id));
        }

        $colorIds = array_filter([$composite->getColorId1(), $composite->getColorId2(), $composite->getColorId3(), $composite->getColorId4()]);
        if (!empty($colorIds)) {
            $composite->colors = $this->getColors($colorIds); 
        }

        return $composite;
    }

    protected function getColors($ids) 
    {
        $query = $this->getDoctrine()->getManager()->createQuery("
            SELECT partial c.{id, valueHex, nameMale}
            FROM ContentBundle:Color c INDEX BY c.id
            WHERE c.id IN (:ids)

        ");
        $query->setParameter('ids', $ids);
        $colors = $query->getResult();

        return array_map(function($id) use ($colors) {
            return $colors[$id];
        }, $ids);
    }
}