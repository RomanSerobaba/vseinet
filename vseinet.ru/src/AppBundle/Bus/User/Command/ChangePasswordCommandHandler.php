<?php 

namespace AppBundle\Bus\User\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Bus\Exception\ValidationException;
use AppBundle\Entity\User;

class ChangePasswordCommandHandler extends MessageHandler
{
    public function handle(ChangePasswordCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository(User::class)->find($this->get('user.identity')->getUser()->getId());
        if (!$user instanceof User) {
            throw new NotFoundHttpException();
        }

        $encoder = $this->get('security.password_encoder');
        if (!$encoder->isPasswordValid($user, $command->password)) {
            throw new ValidationException([
                'password' => 'Неверный пароль', 
            ]);
        }

        if ($command->newPassword === $command->password) {
            throw new ValidationException([
                'newPassword' => 'Совпадает со старым'
            ]);
        }

        if ($command->newPassword !== $command->newPasswordConfirm) {
            throw new ValidationException([
                'newPasswordConfirm' => 'Пароли не совпадают',
            ]);
        }

        $this->get('security.password_encoder')->encodePassword($user, $command->newPassword);

        $em->persist($user);
        $em->flush();

        $this->get('command_bus')->handle(new LoginCompleteCommand(['id' => $user->getId()]));
    }
}
