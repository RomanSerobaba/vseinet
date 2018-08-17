<?php 

namespace AppBundle\Bus\Security\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Doctrine\ORM\NoResultException;
use AppBundle\Bus\Exception\ValidationException;
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
            throw new BadRequestHttpException();
        } 

        // Код 6 цифр
        while (true) {
            $code = rand(100000, 999999);
            $q = $em->createQuery("
                SELECT ut.code
                FROM AppBundle:UserToken AS ut 
                WHERE ut.code = :code 
            ");
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
                $q = $em->createQuery("
                    SELECT ut.hash
                    FROM AppBundle:UserToken AS ut 
                    WHERE ut.hash = :hash 
                ");
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

        if (ContactTypeCode::MOBILE === $contact->getContactTypeCode()) { 
            // @todo отправка смс      
        } else {
            // @todo отправка email      
        }
    }
}
