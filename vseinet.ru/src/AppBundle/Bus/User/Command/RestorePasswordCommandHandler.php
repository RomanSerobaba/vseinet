<?php 

namespace AppBundle\Bus\User\Command;

use AppBundle\Bus\Message\MessageHandler;

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

        $user = $this->get('session')->get('user');
        $this->get('security.password_encoder')->encodePassword($user, $command->password);
        $em->persist($user);
        $em->flush();

        $this->get('session')->getFlashBag()->add('notice', 'Новый пароль успешно сохранен');
    }
}
