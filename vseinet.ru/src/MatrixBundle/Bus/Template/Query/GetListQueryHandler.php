<?php 

namespace MatrixBundle\Bus\Template\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use MatrixBundle\Entity\TradeMatrixTemplate;

class GetListQueryHandler extends MessageHandler
{
    public function handle(GetListQuery $query)
    {
        return $this->getDoctrine()->getManager()->getRepository(TradeMatrixTemplate::class)->findAll();
    }
}
