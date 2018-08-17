<?php 

namespace AppBundle\Bus\Security\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use AppBundle\Entity\User;

class RestorePasswordCommandHandler extends MessageHandler
{
    public function handle(RestorePasswordCommand $command)
    {
        if ($command->password !== $command->passwordConfirm) {
            throw new ValidationException([
                'passwordConfirm' => 'Пароли не совпадают',
            ]);
        }

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository(User::class)->find($this->getUser()->getId());
        if (!$user instanceof User) {
            throw new BadRequestHttpException();
        }
        $password = $this->get('security.password_encoder')->encodePassword($user, $command->password);
        $user->setPassword($password);
        $em->persist($user);
        $em->flush();
    }
}
