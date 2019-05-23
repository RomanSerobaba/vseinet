<?php

namespace AppBundle\Bus\Security\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Exception\ValidationException;
use AppBundle\Entity\UserToken;

class CheckTokenCommandHandler extends MessageHandler
{
    public function handle(CheckTokenCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        if ($command->code) {
            $criteria = ['code' => $command->code];
        } else {
            $criteria = ['hash' => $command->hash];
        }

        $token = $em->getRepository(UserToken::class)->findOneBy($criteria);
        if (!$token instanceof UserToken || $token->getExpiredAt() < new \DateTime()) {
            throw new ValidationException('code', 'Неверный код подтверждения');
        }

        $this->get('session')->set('restore password userId', $token->getUserId());

        $em->remove($token);
        $em->flush();
    }
}
