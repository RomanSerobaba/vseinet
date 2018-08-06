<?php 

namespace MatrixBundle\Bus\Template\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use MatrixBundle\Entity\TradeMatrixTemplate;

class GetQueryHandler extends MessageHandler
{
    public function handle(GetQuery $query)
    {
        $template = $this->getDoctrine()->getManager()->getRepository(TradeMatrixTemplate::class)->find($query->id);
        if (!$template instanceof TradeMatrixTemplate) {
            throw new NotFoundHttpException(sprintf('Шаблон %s не найден', $query->id));
        }

        return $template;
    }
}
