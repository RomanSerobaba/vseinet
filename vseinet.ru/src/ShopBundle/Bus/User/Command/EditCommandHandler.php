<?php 

namespace ShopBundle\Bus\User\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\Contact;
use AppBundle\Entity\Person;
use AppBundle\Entity\User;
use AppBundle\Enum\ContactTypeCode;
use Doctrine\ORM\Query\ResultSetMapping;
use ShopBundle\Entity\BannerMainData;
use ShopBundle\Entity\BannerMainTemplate;
use SupplyBundle\Entity\ViewSupplierOrderItem;
use ReservesBundle\Entity\Shipment;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Exception\ValidatorException;

class EditCommandHandler extends MessageHandler
{
    public function handle(EditCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();
        $id = $currentUser->getId();

        if (!empty($command->mobile) && (!preg_match("/^\s*(\+7){0,1}9[0-9()\- ]+\s*$/", $command->mobile) || strlen(preg_replace("~(\+7|\D+)~isu","", $command->mobile)) != 10)) {
            throw new ValidatorException('Неправильный формат телефона');
        }

        if (!empty($command->email) && !preg_match("/^[a-zA-Z0-9][\w\.-]*@[a-zA-Z0-9][\w\.-]*[a-zA-Z0-9]\.[a-zA-Z][a-zA-Z\.]*[a-zA-Z]$/", $command->email)) {
            throw new ValidatorException('Неправильный формат E-mail');
        }

        $user = $em->getRepository(User::class)->find($id);

        if (!$user) {
            throw new NotFoundHttpException('Пользователь не найден');
        }

        $person = $em->getRepository(Person::class)->find($user->getPersonId());

        if (!$person) {
            throw new NotFoundHttpException('Person не найден');
        }

        $contacts = $em->getRepository(Contact::class)->findBy(['personId' => $person->getId(),]);

        $em->getConnection()->beginTransaction();
        try {
            $user->setCityId($command->geoCityId);
            $user->setIsMarketingSubscribed($command->isMarketingSubscribed);
            $em->persist($user);

            $person->setFirstname($command->firstname);
            $person->setLastname($command->lastname);
            $person->setSecondname($command->secondname);
            $person->setGender($command->gender);
            $em->persist($person);

            foreach ($contacts as $contact) {
                if ($contact->getContactTypeCode() === ContactTypeCode::EMAIL) {
                    $contact->setValue($command->email);
                } elseif ($contact->getContactTypeCode() === ContactTypeCode::MOBILE) {
                    $contact->setValue($command->mobile);
                }elseif ($contact->getContactTypeCode() === ContactTypeCode::PHONE || $contact->getContactTypeCode() === ContactTypeCode::CUSTOM) {
                    $contact->setValue($command->phone);
                }

                $em->persist($contact);
            }

            $em->flush();

            $em->getConnection()->commit();
        } catch (\Exception $ex) {
            $em->getConnection()->rollback();

            throw $ex;
        }
    }
}