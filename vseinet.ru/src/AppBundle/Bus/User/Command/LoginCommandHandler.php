<?php

namespace AppBundle\Bus\User\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Bus\Exception\ValidationException;
use AppBundle\Enum\ContactTypeCode;
use AppBundle\Entity\Contact;
use AppBundle\Entity\User;

class LoginCommandHandler extends MessageHandler
{
    public function handle(LoginCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        if (false === strpos($command->username, '@')) {
            $phone = preg_replace('/\D+/', '', $command->username);
            if (11 === strlen($phone)) {
                $phone = substr($phone, 1);
            }
            $contact = $em->getRepository(Contact::class)->findOneBy([
                'contactTypeCode' => ContactTypeCode::MOBILE,
                'value' => $phone,
            ]);
            if (!$contact instanceof Contact) {
                throw new ValidationException([
                    'username' => 'Пользователь с указанным телефоном не найден', 
                ]);
            }
        } else {            
            $contact = $em->getRepository(Contact::class)->findOneBy([
                'contactTypeCode' => ContactTypeCode::EMAIL,
                'value' => $command->username,
            ]);
            if (!$contact instanceof Contact) {
                throw new ValidationException([
                    'username' => 'Пользователь с указанным email не найден', 
                ]);
            }
        }

        $user = $em->getRepository(User::class)->findOneBy(['personId' => $contact->getPersonId()]);
        if (!$user instanceof User) {
            throw new ValidationException([
                'username' => 'Пользователь не найден', 
            ]);
        } 

        $encoder = $this->get('security.password_encoder');
        if (!$encoder->isPasswordValid($user, $command->password)) {
            throw new ValidationException([
                'password' => 'Неверный пароль', 
            ]);
        }

        $this->get('command_bus')->handle(new LoginCompleteCommand(['id' => $user->getId()]));
    }
}
