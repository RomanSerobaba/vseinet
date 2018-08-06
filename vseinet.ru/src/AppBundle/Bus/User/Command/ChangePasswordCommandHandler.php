<?php 

namespace AppBundle\Bus\User\Command;

use AppBundle\Bus\Message\MessageHandler;

class ChangePasswordCommandHandler extends MessageHandler
{
    public function handle(ChangePasswordCommand $command)
    {
        $user = $this->get('user.identity')->getUser();

        $encoder = $this->get('security.password_encoder');
        if (!$encoder->isPasswordValid($user, $command->password)) {
            throw new ValidationException([
                'password' => 'Неверный пароль', 
            ]);
        }

        if ($command->newPassword !== $command->newPasswordConfirm) {
            throw new ValidationException([
                'newPasswordConfirm' => 'Пароли не совпадают',
            ]);
        }

        $this->get('security.password_encoder')->encodePassword($user, $command->newPassword);

        $em = $this->getDoctrine()->getManager();
        
        $em->persist($user);
        $em->flush();

        $this->get('session')->getFlashBag()->add('notice', 'Новый пароль успешно сохранен');

        $this->get('command_bus')->handle(new LoginCompleteCommand(['id' => $user->getId()]));
    }
}
