<?php 

namespace AppBundle\Bus\User\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Bus\Exception\ValidationException;
use AppBundle\Entity\UserToken;

class CheckTokenCommandHandler extends MessageHandler
{
    public function handle(CheckTokenCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $token = $em->getRepository(UserToken::class)->findOneBy($command->toArray());
        if (!$token instanceof UserToken || $token->getExpiredAt() < new \DateTime()) {
            throw new ValidationException([
                'code' => 'Неверный код подтвержения',
            ]);
        }
        
        $this->get('command_bus')->handle(new LoginCompleteCommand(['id' => $token->getUserId()]));

        $em->remove($token);
        $em->flush();
    }
}
