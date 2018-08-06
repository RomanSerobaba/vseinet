<?php 

namespace AppBundle\Bus\User\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Bus\Exception\ValidationException;
use AppBundle\Entity\Contact;
use AppBundle\Entity\ContactType;
use AppBundle\Entity\Person;
use AppBundle\Entity\User;

class CreateContactCommandHandler extends MessageHandler
{
    public function handle(CreateContactCommand $command)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('uder.identity')->getUser();

        $contact = $em->getRepository(Contact::class)->findOneBy([
            'contactTypeCode' => $command->typeCode,
            'value' => $command->value,
            'personId' => $user->getPersonId(),
        ]);
        if ($contact instanceof Contact) {
            throw new ValidationException([
                'value' => 'Вы уже добавили такой контакт',
            ]);
        }

        $validator = new Validator\ContactValidator($command->typeCode, $command->value);
        $validator->validate();
        $command->value = $validator->getValue();

        if ($command->isMain) {
            $q = $em->createQuery("
                UPDATE AppBundle:Contact AS c 
                SET c.isMain = false 
                WHERE c.contactTypeCode = :typeCode AND c.personId = :personId
            ");
            $q->setParameter('typeCode', $command->typeCode);
            $q->setParameter('personId', $user->getPersonId());
            $q->execute();
        }

        $contact = new Contact();
        $contact->setContactTypeCode($command->typeCode);
        $contact->setValue($command->value);
        $contact->setComment($command->comment);
        $contact->setIsMain($command->isMain);

        $em->persist($contact);
        $em->flush();

        $this->get('uuid.manager')->saveId($command->uuid, $contact->getId());
    }
}
