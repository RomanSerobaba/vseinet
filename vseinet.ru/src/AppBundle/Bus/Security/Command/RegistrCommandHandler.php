<?php 

namespace AppBundle\Bus\Security\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Doctrine\ORM\NoResultException;
use AppBundle\Bus\Exception\ValidationException;
use AppBundle\Enum\ContactTypeCode;
use AppBundle\Entity\Contact;
use AppBundle\Entity\Person;
use AppBundle\Entity\User;
use AppBundle\Entity\Role;
use AppBundle\Entity\Subrole;
use AppBundle\Entity\UserToSubrole;
use AppBundle\Entity\GeoCity;
use AppBundle\Enum\UserRole;

class RegistrCommandHandler extends MessageHandler
{
    public function handle(RegistrCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        if ($command->mobile) {
            $command->mobile = preg_replace('/\D+/', '', $command->mobile);
            if (11 === strlen($command->mobile) && ('7' === $command->mobile[0] || '8' === $command->mobile[0])) {
                $command->mobile = substr($command->mobile, 1);
            }
            if (10 !== strlen($commnad->mobile) || '9' !== $command->mobile[0]) {
                throw new ValidationException([
                    'mobile' => 'Неверный формат мобильного телефона',
                ]);
            }
            $q = $em->createQuery("
                SELECT c
                FROM AppBundle:Contact AS c
                WHERE c.contactTypeCode = :type AND c.value = :value 
            ");
            $q->setParameter('type', ContactTypeCode::MOBILE);
            $q->setParameter('value', $command->mobile);
            try {
                $q->getSingleResult();
                throw new ValidationException([
                    'mobile' => 'Пользователь с указанным телефоном уже существует',
                ]);
            } catch (NoResultException $e) {
            }
        }

        $q = $em->createQuery("
            SELECT c
            FROM AppBundle:Contact AS c
            WHERE c.contactTypeCode = :type AND c.value = :value    
        ");
        $q->setParameter('type', ContactTypeCode::EMAIL);
        $q->setParameter('value', $command->email);
        try {
            $q->getSingleResult();
            throw new ValidationException([
                'email' => 'Пользователь с указанным E-mail  уже существует',
            ]);
        } catch (NoResultException $e) {
        }

        if ($command->password !== $command->passwordConfirm) {
            throw new ValidationException([
                'passwordConfirm' => 'Пароли не совпадают',
            ]);
        }

        $city = $em->getRepository(GeoCity::class)->findOneBy(['name' => $command->city]);
        if (!$city instanceof GeoCity) {
            throw new ValidationException([
                'city' => 'Город не найден',
            ]);
        }

        $person = new Person();
        $person->setLastname($command->lastname);
        $person->setFirstname($command->firstname);
        $person->setSecondname($command->secondname);
        $person->setGender($command->gender);
        if ($command->birthday) {
            if (!$command->birthday instanceof \DateTime) {
                $command->birthday = new \DateTime($command->birthday);
            }
        }
        $person->setBirthday($command->birthday);
        $em->persist($person);
        $em->flush();

        $user = new User();
        $user->setCityId($city->getId());
        $user->setIsMarketingSubscribed($command->isMarketingSubscribed);
        $user->setPersonId($person->getId());
        $user->setRegisteredAt(new \DateTime());
        $password = $this->get('security.password_encoder')->encodePassword($user, $command->password);
        $user->setPassword($password);
        $em->persist($user);
        $em->flush();

        $role = $em->getRepository(Role::class)->findOneBy(['code' => UserRole::CLIENT]);
        if (!$role instanceof Role) {
            throw new BadRequestHttpException();
        }
        $subrole = $em->getRepository(Subrole::class)->findOneBy(['roleId' => $role->getId()]);
        if (!$subrole instanceof Subrole) {
            throw new BadRequestHttpException();
        }
        $u2sr = new UserToSubrole();
        $u2sr->setUserId($user->getId());
        $u2sr->setSubroleId($subrole->getId());
        $em->persist($u2sr);
 
        $contact = new Contact();
        $contact->setPersonId($person->getId());
        $contact->setValue($command->mobile);
        $contact->setContactTypeCode(ContactTypeCode::MOBILE);
        $contact->setCityId($city->getId());
        $contact->setIsMain(true);
        $em->persist($contact);

        $contact = new Contact();
        $contact->setPersonId($person->getId());
        $contact->setValue($command->email);
        $contact->setContactTypeCode(ContactTypeCode::EMAIL);
        $contact->setCityId($city->getId());
        $contact->setIsMain(true);
        $em->persist($contact);

        if (!empty($command->phones) && is_array($command->phones)) {
            // @todo: сделать выборку телефонов из произвольной строки
            $phones = array_filter(array_map('trim', explode(',', $command->phone)));
            if (!empty($phones)) {
                foreach ($phones as $phone) {
                    $contact = new Contact();
                    $contact->setPersonId($person->getId());
                    $contact->setValue($phone);
                    $contact->setContactTypeCode(ContactTypeCode::PHONE);
                    $contact->setCityId($city->getId());
                    $contact->setIsMain(false);
                    $em->persist($contact);
                }
                
            }
        }
        
        $em->flush();
    }
}
