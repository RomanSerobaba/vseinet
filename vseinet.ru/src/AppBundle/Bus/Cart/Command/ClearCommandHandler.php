<?php 

namespace AppBundle\Bus\Cart\Command;

use AppBundle\Bus\Message\MessageHandler;

class ClearCommandHandler extends MessageHandler
{
    public function handle(ClearCommand $command)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if (null !== $user) {
            $q = $em->createQuery("DELETE FROM AppBundle:Cart c WHERE c.userId = :userId ");
            $q->setParameter('userId', $user->getId());
            $q->execute();
        } else {
            $this->get('session')->remove('cart');
        }
    }
}
