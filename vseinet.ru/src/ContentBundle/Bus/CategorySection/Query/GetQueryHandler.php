<?php 

namespace ContentBundle\Bus\CategorySection\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\CategorySection;

class GetQueryHandler extends MessageHandler 
{
    public function handle(GetQuery $query)
    {
        $section = $this->getDoctrine()->getManager()->getRepository(CategorySection::class)->find($query->id);
        if (!$section instanceof CategorySection) {
            throw new NotFoundHttpException(sprintf('Раздел категории с кодом %d не найден', $query->id));
        }

        return $section;
    }
}