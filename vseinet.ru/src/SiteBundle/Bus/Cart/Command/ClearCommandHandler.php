<?php 

namespace SiteBundle\Bus\Cart\Command;

use AppBundle\Bus\Message\MessageHandler;

class ClearCommandHandler extends MessageHandler
{
    public function handle(ClearCommand $command)
    {
        if ($this->get('user.identity')->isAuthorized()) {
            $q = $this->getDoctrine()->getManager()->createQuery("DELETE FROM SiteBundle:Cart c WHERE c.userId = :userId ");
            $q->setParameter('userId', $this->get('user.identity')->getUser()->getId());
            $q->execute();
        } else {
            $this->get('session')->remove('cart');
        }
    }
}
