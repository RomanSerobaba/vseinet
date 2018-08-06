<?php 

namespace AppBundle\Bus\User\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\User;
use AppBundle\Entity\Person;
use GeoBundle\Entity\GeoCity;

class UpdateCommandHandler extends MessageHandler
{
    public function handle(UpdateCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository(User::class)->find($this->get('user.identity')->getUser()->getId());
        if (!$user instanceof User) {
            throw new NotFoundHttpException();
        }
        // if ($command->cityId) {
        //     $city = $em->getRepository(GeoCity::class)->findOneBy($command->cityId);
        //     if (!$city instanceof GeoCity) {
        //         throw new NotFoundHttpException();
        //     }
        // }
        // $user->setCityId($command->cityId);
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
        if ($command->birthday) {
            if (!$command->birthday instanceof \DateTime) {
                $command->birthday = new \DateTime($command->birthday);
            }
        }
        $person->setBirthday($command->birthday);
        $em->persist($person);

        $em->flush();

        $this->get('session')->getFlashBag()->add('notice', 'Ваш профиль успешно обновлен');

        $this->get('command_bus')->handle(new LoginCompleteCommand(['id' => $user->getId()]));
    }
}
