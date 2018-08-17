<?php

namespace AppBundle\Bus\Favorite\Command;

use AppBundle\Bus\Message\MessageHandler;

class ClearCommandHandler extends MessageHandler
{
    public function handle(ClearCommand $command)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if (null !== $user) {
            $q = $em->createQuery("DELETE FROM AppBundle:Favorite f WHERE f.userId = :userId");
            $q->setParameter('userId', $user->getId());
            $q->execute();
        } else {
            $this->get('session')->remove('favorites');
        }
    }
}
