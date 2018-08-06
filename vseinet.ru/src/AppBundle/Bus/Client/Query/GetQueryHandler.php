<?php 

namespace AppBundle\Bus\Client\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Bus\Client\Query\DTO\Client;

class GetQueryHandler extends MessageHandler
{
    public function handle(GetQuery $query)
    {
        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT 
                NEW AppBundle\Bus\Client\Query\DTO\Client (
                    c.id,
                    c.name,
                    c.redirectUris
                )
            FROM AppBundle:Client c 
            WHERE c.id = :id  
        ");
        $q->setParameter('id', $query->id);
        $client = $q->getSingleResult();
        if (!$client instanceof Client) {
            throw new NotFoundHttpException(sprintf('Слиент API %d не найден', $query->id));
        }

        return $client;
    }
}