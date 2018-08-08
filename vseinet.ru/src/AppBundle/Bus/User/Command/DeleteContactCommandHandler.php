<?php 

namespace AppBundle\Bus\User\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\Contact;

class DeleteContactCommandHandler extends MessageHandler
{
    public function handle(DeleteContactCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $contact = $em->getRepository(Contact::class)->find($command->id);
        if (!$contact instanceof Contact) {
            throw new NotFoundHttpException();
        } 

        $em->remove($contact);
        $em->flush();
    }
}
