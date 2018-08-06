<?php 

namespace ContentBundle\Bus\Color\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Color;

class GetQueryHandler extends MessageHandler
{
    public function handle(GetQuery $query)
    {
        $color = $this->getDoctrine()->getManager()->getRepository(Color::class)->find($query->id);
        if (!$color instanceof Color) {
            throw new NotFoundHttpException('Цвет не найден');
        }

        return $color;
    }
}