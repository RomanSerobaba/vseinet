<?php 

namespace AppBundle\Bus\Security\Command;

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
                'code' => 'Неверный код подтверждения',
            ]);
        }
        
        $em->remove($token);
        $em->flush();
    }
}
