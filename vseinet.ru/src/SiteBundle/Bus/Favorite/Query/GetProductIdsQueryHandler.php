<?php 

namespace SiteBundle\Bus\Favorite\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetProductIdsQueryHandler extends MessageHandler
{
    public function handle(GetProductIdsQuery $query)
    {
        if ($this->get('user.identity')->isAuthorized()) {
            $q = $this->getDoctrine()->getManager()->createQuery("
                SELECT f.baseProductId
                FROM SiteBundle:Favorite f 
                WHERE f.userId = :userId 
            ");
            $q->setParameter('userId', $this->get('user.identity')->getUser()->getId());

            return $q->getResult('ListHydrator');
        }

        return $this->get('session')->get('favorites', []);
    }
}
