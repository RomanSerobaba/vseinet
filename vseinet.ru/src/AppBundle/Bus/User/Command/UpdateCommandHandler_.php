<?php 

namespace AppBundle\Bus\User\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\Contact;
use AppBundle\Entity\ContactType;
use AppBundle\Entity\Person;
use AppBundle\Entity\User;

class UpdateCommandHandler extends MessageHandler
{
    public function handle(UpdateCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var User $user */
        $user = $em->getRepository(User::class)->find($command->id);
        $user->setCityId($command->cityId);
        $em->persist($user);

        /** @var Person $person */
        $person = $user->getPerson();
        $person->setLastname($command->lastname);
        $person->setFirstname($command->firstname);
        $person->setSecondname($command->secondname);
        $person->setGender($command->gender);
        $person->setBirthday(($command->birthday instanceof \DateTime) ? $command->birthday : new \DateTime($command->birthday));
        $em->persist($person);

        /** @var Contact $contactMobile */
        $contactMobile = $em->getRepository(Contact::class)->findOneBy(['id' => $command->mobileId, 'personId' => $person->getId(), 'contactTypeCode' => ContactType::CODE_MOBILE,]);
        if (!$contactMobile) {
            $contactMobile = new Contact();
            $contactMobile->setPerson($person);
        }
        $contactMobile->setContactTypeCode(ContactType::CODE_MOBILE);
        $contactMobile->setValue($command->mobile);
        $contactMobile->setCityId($command->cityId);
        $em->persist($contactMobile);

        /** @var Contact $contactEmail */
        $contactEmail = $em->getRepository(Contact::class)->findOneBy(['id' => $command->emailId, 'personId' => $person->getId(), 'contactTypeCode' => ContactType::CODE_EMAIL,]);
        if (!$contactEmail) {
            $contactEmail = new Contact();
            $contactEmail->setPerson($person);
        }
        $contactEmail->setContactTypeCode(ContactType::CODE_EMAIL);
        $contactEmail->setValue($command->email);
        $contactEmail->setCityId($command->cityId);
        $em->persist($contactEmail);

        if (!empty($command->phones) && is_array($command->phones)) {
            foreach ($command->phones as $id => $phone) {
                /** @var Contact $contactPhone */
                if (!empty($id)) {
                    $contactPhone = $em->getRepository(Contact::class)->findOneBy(['id' => $id, 'personId' => $person->getId(), 'contactTypeCode' => ContactType::CODE_PHONE,]);
                    if (!$contactPhone) {
                        $contactPhone = new Contact();
                    }
                } else {
                    $contactPhone = new Contact();
                }

                $contactPhone->setPerson($person);
                $contactPhone->setContactTypeCode(ContactType::CODE_PHONE);
                $contactPhone->setValue($phone);
                $contactPhone->setCityId($command->cityId);
                $em->persist($contactPhone);
            }
        }

        $em->flush();
    }
}