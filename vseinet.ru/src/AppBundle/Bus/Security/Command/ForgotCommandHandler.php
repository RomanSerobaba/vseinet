<?php

namespace AppBundle\Bus\Security\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Doctrine\ORM\NoResultException;
use AppBundle\Exception\ValidationException;
use AppBundle\Enum\ContactTypeCode;
use AppBundle\Entity\Contact;
use AppBundle\Entity\User;
use AppBundle\Entity\UserToken;

class ForgotCommandHandler extends MessageHandler
{
    public function handle(ForgotCommand $command)
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
                'isMain' => true,
            ]);
            if (!$contact instanceof Contact) {
                throw new ValidationException('username', 'Пользователь с указанным телефоном не найден');
            }
        } else {
            $contact = $em->getRepository(Contact::class)->findOneBy([
                'contactTypeCode' => ContactTypeCode::EMAIL,
                'value' => $command->username,
                'isMain' => true,
            ]);
            if (!$contact instanceof Contact) {
                throw new ValidationException('username', 'Пользователь с указанным email не найден');
            }
        }

        $user = $em->getRepository(User::class)->findOneBy(['personId' => $contact->getPersonId()]);
        if (!$user instanceof User) {
            throw new BadRequestHttpException();
        }

        // Код 6 цифр
        while (true) {
            $a = $c = rand(1, 9);
            $b = rand(1, 9);
            $d = rand(0, 9);
            $code = $a.$b.$c.$d;
            $q = $em->createQuery('
                SELECT ut.code
                FROM AppBundle:UserToken AS ut
                WHERE ut.code = :code
            ');
            $q->setParameter('code', $code);
            try {
                $q->getSingleScalarResult();
            } catch (NoResultException $e) {
                break;
            }
        }
        if (ContactTypeCode::EMAIL === $contact->getContactTypeCode()) {
            // Hash для ссылки 60 hex-символов
            while (true) {
                $hash = bin2hex(random_bytes(30));
                $q = $em->createQuery('
                    SELECT ut.hash
                    FROM AppBundle:UserToken AS ut
                    WHERE ut.hash = :hash
                ');
                $q->setParameter('hash', $hash);
                try {
                    $q->getSingleScalarResult();
                } catch (NoResultException $e) {
                    break;
                }
            }
        } else {
            $hash = null;
        }

        $lifetime = ContactTypeCode::MOBILE === $contact->getContactTypeCode() ? 1 : 2;

        $token = new UserToken();
        $token->setCode($code);
        $token->setHash($hash);
        $token->setUserId($user->getId());
        $token->setExpiredAt(new \DateTime("+{$lifetime} minute"));

        $em->persist($token);
        $em->flush();

        $api = $this->get('site.api.client');

        if (ContactTypeCode::MOBILE === $contact->getContactTypeCode()) {
            try {
                $api->post('/api/v1/sms/', [], [
                    'phone' => $contact->getValue(),
                    'text' => 'Код подтверждения для восстановления пароля: '.$code,
                ]);
            } catch (BadRequestHttpException $e) {
                return null;
            }
        } else {
            try {
                $api->post('/api/v1/email/', [], [
                    'mobile' => $contact->getValue(),
                    'subject' => 'Восстановление пароля',
                    'text' => 'Код подтверждения для восстановления пароля: <b>'.$code.'</b><br/>Либо вы можете просто перейти по <a href="https://vseinet.ru/check/token/?hash='.$hash.'">ссылке</a>',
                ]);
            } catch (BadRequestHttpException $e) {
                return null;
            }
        }
    }
}
