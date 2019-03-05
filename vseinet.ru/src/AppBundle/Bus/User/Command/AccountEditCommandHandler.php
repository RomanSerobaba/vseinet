<?php

namespace AppBundle\Bus\User\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\User;
use AppBundle\Entity\Person;
use AppBundle\Entity\GeoCity;

class AccountEditCommandHandler extends MessageHandler
{
    public function handle(AccountEditCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository(User::class)->find($this->getUser()->getId());
        if (!$user instanceof User) {
            throw new NotFoundHttpException();
        }
        if ($command->geoCityId) {
            $geoCity = $em->getRepository(GeoCity::class)->find($command->geoCityId);
        } else {
            $geoCity = $em->getRepository(GeoCity::class)->findOneByName($command->geoCityName);
        }
        if (!$geoCity instanceof GeoCity || $geoCity->getName() != $command->geoCityName) {
            throw new NotFoundHttpException();
        }
        $user->setGeoCityId($geoCity->getId());
        $user->setIsMarketingSubscribed($command->isMarketingSubscribed);
        $em->persist($user);

        $person = $em->getRepository(Person::class)->find($user->getPersonId());
        if (!$person instanceof Person) {
            throw new NotFoundHttpException();
        }
        $person->setLastname($command->lastname);
        $person->setFirstname($command->firstname);
        $person->setSecondname($command->secondname);
        $person->setGender($command->gender);
        $person->setBirthday($command->birthday);

        $em->persist($person);
        $em->flush();
    }
}
