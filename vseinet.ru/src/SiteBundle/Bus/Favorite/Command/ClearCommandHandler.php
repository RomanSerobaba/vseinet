<?php

namespace SiteBundle\Bus\Favorite\Command;

use AppBundle\Bus\Message\MessageHandler;

class ClearCommandHandler extends MessageHandler
{
    public function handle(ClearCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        if ($this->get('user.identity')->isAuthorized()) {
            $q = $this->getDoctrine()->getManager()->createQuery("DELETE FROM SiteBundle:Favorite f WHERE f.userId = :userId");
            $q->setParameter('userId', $this->get('user.identity')->getUser()->getId());
            $q->execute();
        } else {
            $this->get('session')->remove('favorites');
        }
    }
}
