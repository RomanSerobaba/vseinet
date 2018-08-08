<?php 

namespace AppBundle\Bus\User\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Bus\Exception\ValidationException;
use AppBundle\Entity\Contact;

class CreateContactCommandHandler extends MessageHandler
{
    public function handle(CreateContactCommand $command)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('user.identity')->getUser();

        $validator = new Validator\ContactValidator($command->typeCode, $command->value);
        $validator->validate();
        $command->value = $validator->getValue();

        $contact = $em->getRepository(Contact::class)->findOneBy([
            'contactTypeCode' => $command->typeCode,
            'value' => $command->value,
            'personId' => $user->person->getId(),
        ]);
        if ($contact instanceof Contact) {
            throw new ValidationException([
                'value' => 'Вы уже добавили такой контакт',
            ]);
        }

        if ($command->isMain) {
            $same = $em->getRepository(Contact::class)->findOneBy([
                'contactTypeCode' => $command->typeCode,
                'value' => $command->value,    
                'isMain' => true,
            ]);
            if ($same instanceof Contact) {
                throw new ValidationException([
                    'value' => 'Вы не можите назначить этот контакт основным',
                ]);
            }

            $q = $em->createQuery("
                UPDATE AppBundle:Contact AS c 
                SET c.isMain = false 
                WHERE c.contactTypeCode = :typeCode AND c.personId = :personId
            ");
            $q->setParameter('typeCode', $command->typeCode);
            $q->setParameter('personId', $user->person->getId());
            $q->execute();
        }

        $contact = new Contact();
        $contact->setContactTypeCode($command->typeCode);
        $contact->setValue($command->value);
        $contact->setPersonId($user->person->getId());
        $contact->setComment($command->comment);
        $contact->setIsMain($command->isMain);

        $em->persist($contact);
        $em->flush();

        $command->id = $contact->getId();
    }
}
