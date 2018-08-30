<?php 

namespace AppBundle\Bus\User\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\Contact;
use AppBundle\Entity\User;
use AppBundle\Enum\ContactTypeCode;

class IdentifyCommandHandler extends MessageHandler
{
    public function handle(IdentifyCommand $command)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if (null !== $user) {
            $this->updateUserContacts($command->userData, $user);    
        } else {
            $contact = $em->getRepository(Contact::class)->findOneBy([
                'value' => $command->userData->phone,
                'isMain' => true,
            ]);
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
            $comuser = $em->getRepository(Comuser::class)->findOneBy([
                'phone' => [$command->userData->phone, $command->userData->additionalPhone],
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
        
        $em->flush();
    }

    protected function updateUserContacts($userData, $user)
    {
        $em = $this->getDoctrine()->getManager();

        $contact = $em->getRepository(Contact::class)->findOneBy([
            'personId' => $user->person->getId(),
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
            $contact->setPersonId($user->person->getId());
            $em->persist($contact);
        }
        $userData->contactIds[] = $contact->getId();

        if ($userData->additionalPhone) {
            $contact = $em->getRepository(Contact::class)->findOneBy([
                'personId' => $user->person->getId(),
                'value' => $userData->additionalPhone,
            ]);
            if (!$contact instanceof Contact) {
                $contact = new Contact();
                if (10 === strlen($userData->additionalPhone) && 9 == $userData->additionalPhone[0]) {
                    $contact->setContactTypeCode(ContactTypeCode::MOBILE);
                } else {
                    $contact->setContactTypeCode(ContactTypeCode::PHONE);
                }
                $contact->setValue($userData->value);
                $contact->setPersonId($user->person->getId());
                $em->persist($contact);
            }
            $userData->contactIds[] = $contact->getId();
        }

        if ($userData->email) {
            $contact = $em->getRepository(Contact::class)->findOneBy([
                'personId' => $user->person->getId(),
                'value' => $userData->email,
            ]);
            if (!$contact instanceof Contact) {
                $contact = new Contact();
                $contact->setContactTypeCode(ContactTypeCode::EMAIL);
                $contact->setValue($userData->email);
                $contact->setPersonId($user->person->getId());
                $em->persist($contact);
            }
            $userData->contactIds[] = $contact->getId();
        }

        $userData->userId = $user->getId();    
    }
}