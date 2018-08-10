<?php 

namespace AppBundle\Bus\User\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Bus\Exception\ValidationException;
use Doctrine\ORM\NoResultException;
use AppBundle\Enum\ContactTypeCode;
use AppBundle\Entity\Contact;

class AddContactCommandHandler extends MessageHandler
{
    public function handle(AddContactCommand $command)
    {
        if (ContactTypeCode::MOBILE === $command->typeCode || ContactTypeCode::PHONE === $command->typeCode) {
            $command->value = preg_replace('/\D+/', '', $command->value);
            if (11 === strlen($command->value) && ('7' === $command->value[0] || '8' === $command->value)) {
                $command->value = substr($command->value, 1);
            }
        }
        if (ContactTypeCode::MOBILE === $command->typeCode) {
            if (10 !== strlen($command->value) || '9' !== $command->value[0]) {
                throw new ValidationException([
                    'value' => 'Неверный формат мобильного номера телефона',
                ]);
            }
        }
        if (ContactTypeCode::PHONE === $command->typeCode) {
            if (!in_array(strlen($command->value), [6, 7, 10])) {
                throw new ValidationException([
                    'value' => 'Неверный формат номера телефона',
                ]);
            }
        }
        if (ContactTypeCode::EMAIL === $command->typeCode) {
            if (false === strpos($command->value, '@')) {
                throw new ValidationException([
                    'value' => 'Неверный формат email',
                ]);
            }
        }

        $em = $this->getDoctrine()->getManager();
        $user = $this->get('user.identity')->getUser();

        $q = $em->createQuery("
            SELECT 1 
            FROM AppBundle:Contact AS c 
            WHERE c.id != :id AND c.contactTypeCode = :contactTypeCode AND c.value = :value AND c.personId = :personId
        ");
        $q->setParameter('id', $command->id);
        $q->setParameter('contactTypeCode', $command->typeCode);
        $q->setParameter('value', $command->value);
        $q->setParameter('personId', $user->getPersonId());
        try {
            $q->getSingleScalarResult();
            throw new ValidationException([
                'value' => 'Вы уже добавили такой контакт',
            ]);
        } catch (NoResultException $e) {
        }

        if ($command->id) {
            $contact = $em->getRepository(Contact::class)->find($command->id);
            if (!$contact instanceof Contact || $contact->getContactTypeCode() !== $command->typeCode) {
                throw new NotFoundHttpException();
            }
        } else {
            $contact = new Contact();
        }

        if ($command->isMain) {
            $q = $em->createQuery("
                UPDATE AppBundle:Contact AS c 
                SET c.isMain = false 
                WHERE c.id != :id AND c.contactTypeCode = :contactTypeCode AND c.personId = :personId
            ");
            $q->setParameter('id', $command->id);
            $q->setParameter('contactTypeCode', $command->typeCode);
            $q->setParameter('personId', $user->getPersonId());
            $q->execute();
        }

        $contact->setContactTypeCode($command->typeCode);
        $contact->setValue($command->value);
        $contact->setPersonId($user->getPersonId());
        $contact->setComment($command->comment);
        $contact->setIsMain($command->isMain);

        $em->persist($contact);
        $em->flush();

        $command->id = $contact->getId();
    }
}
