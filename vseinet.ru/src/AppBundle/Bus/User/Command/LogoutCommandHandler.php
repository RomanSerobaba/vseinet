<?php 

namespace AppBundle\Bus\User\Command;

use AppBundle\Bus\Message\MessageHandler;

class LogoutCommandHandler extends MessageHandler
{
    public function handle(LogoutCommand $command)
    {
        $this->get('user.identity')->setUser(null);
    }
}
