<?php

namespace AppBundle\Bus\User\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\Contact;
use AppBundle\Entity\User;
use AppBundle\Entity\Comuser;
use AppBundle\Enum\ContactTypeCode;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class IdentifyCommandHandler extends MessageHandler
{
    public function handle(IdentifyCommand $command)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if (null !== $user && !$user->isEmployee() && empty($command->userData->userId) && empty($command->userData->comuserId)) {
            $this->updateUserContacts($command->userData, $user);
        } elseif (!empty($command->userData->userId)) {
            $user = $em->getRepository(User::class)->find($command->userData->userId);

            if (!$user instanceof User) {
                throw new NotFoundHttpException(sprintf('Пользователь с идентификатором %d не найден', $command->userData->userId));
            }

            $this->updateUserContacts($command->userData, $user);
        } elseif (!empty($command->userData->comuserId)) {
            $comuser = $em->getRepository(Comuser::class)->find($command->userData->comuserId);

            if (!$comuser instanceof Comuser) {
                throw new NotFoundHttpException(sprintf('Гостевой пользователь с идентификатором %d не найден', $command->userData->userId));
            }

            $comuser->setFullname($command->userData->fullname ?? $comuser->getFullname());
            $comuser->setPhone($command->userData->phone ?? $comuser->getPhone());
            $comuser->setAdditionalPhone($command->userData->additionalPhone ?? $comuser->getAdditionalPhone());
            $comuser->setEmail($command->userData->email ?? $comuser->getEmail());
            $em->persist($comuser);
        } else {
            $contact = $em->getRepository(Contact::class)->findOneBy([
                'value' => $command->userData->phone,
                'isMain' => true,
            ]);

            if (!$contact instanceof Contact) {
                $contact = $em->getRepository(Contact::class)->findOneBy([
                    'value' => $command->userData->email,
                    'isMain' => true,
                ]);
            }

            if ($contact instanceof Contact) {
                $user = $em->getRepository(User::class)->findOneBy([
                    'personId' => $contact->getPersonId(),
                ]);
                if ($user instanceof User) {
                    $this->updateUserContacts($command->userData, $user);
                }
            }
        }

        if (null === $command->userData->userId) {
            $phones = [];

            if (!empty($command->userData->phone)) {
                $phones[] = $command->userData->phone;
            }

            if (!empty($command->userData->additionalPhone)) {
                $phones[] = $command->userData->additionalPhone;
            }

            if (!empty($phones)) {
                $comuser = $em->getRepository(Comuser::class)->findOneBy([
                    'phone' => $phones,
                ]);
                if (!$comuser instanceof Comuser) {
                    $comuser = new Comuser();
                    $comuser->setFullname($command->userData->fullname);
                    $comuser->setPhone($command->userData->phone);
                    $comuser->setAdditionalPhone($command->userData->additionalPhone);
                    $comuser->setEmail($command->userData->email);
                    $em->persist($comuser);
                }
                $command->userData->comuserId = $comuser->getId();
            }
        }

        $em->flush();

        return $command->userData;
    }

    protected function updateUserContacts($userData, $user)
    {
        $em = $this->getDoctrine()->getManager();

        if ($userData->phone) {
            $contact = $em->getRepository(Contact::class)->findOneBy([
                'personId' => $user->getPersonId(),
                'value' => $userData->phone,
            ]);
            if (!$contact instanceof Contact) {
                $contact = new Contact();
                if (9 == $userData->phone[0]) {
                    $contact->setContactTypeCode(ContactTypeCode::MOBILE);
                } else {
                    $contact->setContactTypeCode(ContactTypeCode::PHONE);
                }
                $contact->setValue($userData->phone);
                $contact->setPersonId($user->getPersonId());
                $em->persist($contact);
            }
            $userData->contactIds[] = $contact->getId();
        }

        if ($userData->additionalPhone) {
            $contact = $em->getRepository(Contact::class)->findOneBy([
                'personId' => $user->getPersonId(),
                'value' => $userData->additionalPhone,
            ]);
            if (!$contact instanceof Contact) {
                $contact = new Contact();
                if (10 === strlen($userData->additionalPhone) && 9 == $userData->additionalPhone[0]) {
                    $contact->setContactTypeCode(ContactTypeCode::MOBILE);
                } else {
                    $contact->setContactTypeCode(ContactTypeCode::PHONE);
                }
                $contact->setValue($userData->additionalPhone);
                $contact->setPersonId($user->getPersonId());
                $em->persist($contact);
            }
            $userData->contactIds[] = $contact->getId();
        }

        if ($userData->email) {
            $contact = $em->getRepository(Contact::class)->findOneBy([
                'personId' => $user->getPersonId(),
                'value' => $userData->email,
            ]);
            if (!$contact instanceof Contact) {
                $contact = new Contact();
                $contact->setContactTypeCode(ContactTypeCode::EMAIL);
                $contact->setValue($userData->email);
                $contact->setPersonId($user->getPersonId());
                $em->persist($contact);
            }
            $userData->contactIds[] = $contact->getId();
        }

        $userData->userId = $user->getId();
    }
}
