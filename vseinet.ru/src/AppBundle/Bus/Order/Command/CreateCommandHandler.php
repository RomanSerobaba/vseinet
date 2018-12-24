<?php 

namespace AppBundle\Bus\Order\Command;

use AppBundle\Bus\Message\MessageHandler;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $command->id = 1;
    }
}
