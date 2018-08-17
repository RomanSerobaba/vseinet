<?php 

namespace AppBundle\Bus\Security\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Bus\Exception\ValidationException;
use AppBundle\Entity\User;

class ChangePasswordCommandHandler extends MessageHandler
{
    public function handle(ChangePasswordCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository(User::class)->find($this->getUser()->getId());
        if (!$user instanceof User) {
            throw new NotFoundHttpException();
        }

        $encoder = $this->get('security.password_encoder');
        if (!$encoder->isPasswordValid($user, $command->password)) {
            throw new ValidationException([
                'password' => 'Введен неверный пароль', 
            ]);
        }

        if ($command->newPassword === $command->password) {
            throw new ValidationException([
                'newPassword' => 'Новый пароль совпадает со старым'
            ]);
        }

        if ($command->newPassword !== $command->newPasswordConfirm) {
            throw new ValidationException([
                'newPasswordConfirm' => 'Новые пароли не совпадают',
            ]);
        }

        $password = $this->get('security.password_encoder')->encodePassword($user, $command->newPassword);
        $user->setPassword($password);
        $em->persist($user);
        $em->flush();
    }
}
