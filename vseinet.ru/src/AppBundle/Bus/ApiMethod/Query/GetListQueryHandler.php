<?php 

namespace AppBundle\Bus\ApiMethod\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\ApiMethodSection;
use AppBundle\Entity\ApiMethod;

class GetListQueryHandler extends MessageHandler
{
    public function handle(GetListQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $section = $em->getRepository(ApiMethodSection::class)->find($query->sectionId);
        if (!$section instanceof ApiMethodSection) {
            throw new NotFoundHttpException(sprintf('Раздел API %d не найден', $query->sectionId));
        }

        return $em->getRepository(ApiMethod::class)->findBy(['sectionId' => $section->getId()], ['sortOrder' => 'ASC']);
    }
}