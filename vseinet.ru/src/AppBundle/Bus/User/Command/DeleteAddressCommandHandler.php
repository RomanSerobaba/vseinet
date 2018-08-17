<?php 

namespace AppBundle\Bus\User\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\GeoAddress;

class DeleteAddressCommandHandler extends MessageHandler
{
    public function handle(DeleteAddressCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $address = $em->getRepository(GeoAddress::class)->find($command->id);
        if (!$address instanceof GeoAddress) {
            throw new NotFoundHttpException();
        } 

        $em->remove($address);
        $em->flush();
    }
}
