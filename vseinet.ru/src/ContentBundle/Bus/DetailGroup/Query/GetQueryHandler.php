<?php 

namespace ContentBundle\Bus\DetailGroup\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\DetailGroup;

class GetQueryHandler extends MessageHandler
{
    public function handle(GetQuery $query)
    {
        $group = $this->getDoctrine()->getManager()->getRepository(DetailGroup::class)->find($query->id);
        if (!$group instanceof DetailGroup) {
            throw new NotFoundHttpException(sprintf('Группа характеристик %d не найдена', $query->id));
        }

        return $group;
    }
}