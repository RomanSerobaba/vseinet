<?php 

namespace AppBundle\Bus\User\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Bus\Exception\ValidationException;
use AppBundle\Entity\Contact;

class UpdateContactCommandHandler extends MessageHandler
{
    public function handle(UpdateContactCommand $command)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('user.identity')->getUser();

        $contact = $em->getRepository(Contact::class)->find($command->id);
        if (!$contact instanceof Contact) {
            throw new NotFoundHttpException();
        }

        $validator = new Validator\ContactValidator($command->typeCode, $command->value);
        $validator->validate();
        $command->value = $validator->getValue();

        $duplicate = $em->getRepository(Contact::class)->findOneBy([
            'contactTypeCode' => $command->typeCode,
            'value' => $command->value,
            'personId' => $user->person->getId(),
        ]);
        if ($duplicate instanceof Contact && $duplicate !== $contact) {
            throw new ValidationException([
                'value' => 'У Вас уже есть такой контакт',
            ]);
        }

        if ($command->isMain) {
            $same = $em->getRepository(Contact::class)->findOneBy([
                'contactTypeCode' => $command->typeCode,
                'value' => $command->value,    
                'isMain' => true,
            ]);
            if ($same instanceof Contact && $same->getPersonId() !== $user->person->getId()) {
                throw new ValidationException([
                    'value' => 'Вы не можите назначить этот контакт основным',
                ]);
            }

            $q = $em->createQuery("
                UPDATE AppBundle:Contact AS c 
                SET c.isMain = false 
                WHERE c.contactTypeCode = :typeCode AND c.personId = :personId AND c.id != :id
            ");
            $q->setParameter('typeCode', $contact->getContactTypeCode());
            $q->setParameter('personId', $user->person->getId());
            $q->setParameter('id', $contact->getId());
            $q->execute();
        }

        $contact->setValue($command->value);
        $contact->setComment($command->comment);
        $contact->setIsMain($command->isMain);

        $em->persist($contact);
        $em->flush();
    }
}
