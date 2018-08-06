<?php 

namespace AppBundle\Bus\ResourceGroup\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\ResourceGroup;

class GetQueryHandler extends MessageHandler
{
    public function handle(GetQuery $query)
    {
        $group = $this->getDoctrine()->getManager()->getRepository(ResourceGroup::class)->find($query->id);
        if (!$group instanceof ResourceGroup) {
            throw new NotFoundHttpException(sprintf('Группа ресурсов %d не найдена', $query->id));
        }

        return $group;
    }
}