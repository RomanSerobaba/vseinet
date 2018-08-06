<?php 

namespace AccountingBundle\Bus\Clients\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\Contact;
use AppBundle\Entity\ContactType;
use AppBundle\Entity\Person;
use AppBundle\Entity\User;
use SupplyBundle\Entity\Supplier;
use AccountingBundle\Entity\Counteragent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Exception\ValidatorException;

class SaveClientCommandHandler extends MessageHandler
{
    public function handle(SaveClientCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        if (empty($command->id)) { // new user
            $this->get('command_bus')->handle(new \AppBundle\Bus\User\Command\RegisterCommand([
                    'lastname' => $command->lastname,
                    'firstname' => $command->firstname,
                    'secondname' => $command->secondname,
                    'gender' => $command->gender,
                    'mobile' => $command->mobile,
                    'phones' => $command->phones,
                    'email' => $command->email,
                    'password' => $command->password,
                    'password2' => $command->password,
                    'cityId' => $command->cityId,
                    'isMarketingSubscribed' => $command->isMarketingSubscribed,
                    'isTransactionalSubscribed' => $command->isTransactionalSubscribed,
                ])
            );
        } else {
            $user = $em->getRepository(User::class)->findOneBy(['id' => $command->id,]);
            if (!$user instanceof User) {
                throw new NotFoundHttpException('User not found');
            }

            if (!empty($command->mobile) && (!preg_match("/^\s*(\+7){0,1}9[0-9()\- ]+\s*$/", $command->mobile) || strlen(preg_replace("~(\+7|\D+)~isu","", $command->mobile)) != 10)) {
                throw new ValidatorException('Неправильный формат телефона');
            }

            if (!empty($command->email) && !preg_match("/^[a-zA-Z0-9][\w\.-]*@[a-zA-Z0-9][\w\.-]*[a-zA-Z0-9]\.[a-zA-Z][a-zA-Z\.]*[a-zA-Z]$/", $command->email)) {
                throw new ValidatorException('Неправильный формат E-mail');
            }

            $user->setCityId($command->cityId);
            $user->setIsMarketingSubscribed($command->isMarketingSubscribed);
            $user->setIsTransactionalSubscribed($command->isTransactionalSubscribed);
            $em->persist($user);

            $person = $em->getRepository(Person::class)->findOneBy(['id' => $user->getPersonId(),]);

            if ($person instanceof Person) {
                $person->setFirstname($command->firstname);
                $person->setLastname($command->lastname);
                $person->setSecondname($command->secondname);
                $person->setGender($command->gender);

                $em->persist($person);

                $contactMobile = $em->getRepository(Contact::class)->findOneBy(['id' => $command->contactMobileId, 'personId' => $person->getId(), 'contactTypeCode' => ContactType::CODE_MOBILE,]);
                if (!$contactMobile) {
                    $contactMobile = new Contact();
                }
                $contactMobile->setPersonId($person->getId());
                $contactMobile->setContactTypeCode(ContactType::CODE_MOBILE);
                $contactMobile->setValue($command->mobile);
                $contactMobile->setCityId($command->cityId);
                $em->persist($contactMobile);

                $contactEmail = $em->getRepository(Contact::class)->findOneBy(['id' => $command->contactEmailId, 'personId' => $person->getId(), 'contactTypeCode' => ContactType::CODE_EMAIL,]);
                if (!$contactEmail) {
                    $contactEmail = new Contact();
                }
                $contactEmail->setPersonId($person->getId());
                $contactEmail->setValue($command->email);
                $contactEmail->setContactTypeCode(ContactType::CODE_EMAIL);
                $contactEmail->setCityId($command->cityId);
                $em->persist($contactEmail);

                if (!empty($command->phones) && is_array($command->phones)) {
                    foreach ($command->phones as $id => $phone) {
                        if (!empty($id)) {
                            $contactPhone = $em->getRepository(Contact::class)->findOneBy(['id' => $id, 'personId' => $person->getId(), 'contactTypeCode' => ContactType::CODE_PHONE,]);
                            if (!$contactPhone) {
                                $contactPhone = new Contact();
                            }
                        } else {
                            $contactPhone = new Contact();
                        }

                        $contactPhone->setPersonId($person->getId());
                        $contactPhone->setContactTypeCode(ContactType::CODE_PHONE);
                        $contactPhone->setValue($phone);
                        $contactPhone->setCityId($command->cityId);
                        $em->persist($contactPhone);
                    }
                }

                if (!empty($command->skype)) {
                    $contactSkype = $em->getRepository(Contact::class)->findOneBy(['personId' => $person->getId(), 'contactTypeCode' => ContactType::CODE_SKYPE,]);
                    if (!$contactSkype) {
                        $contactSkype = new Contact();
                    }
                    $contactSkype->setPersonId($person->getId());
                    $contactSkype->setValue($command->skype);
                    $contactSkype->setContactTypeCode(ContactType::CODE_SKYPE);
                    $contactSkype->setCityId($command->cityId);
                    $em->persist($contactSkype);
                }

                if (!empty($command->icq)) {
                    $contactIcq = $em->getRepository(Contact::class)->findOneBy(['personId' => $person->getId(), 'contactTypeCode' => ContactType::CODE_ICQ,]);
                    if (!$contactIcq) {
                        $contactIcq = new Contact();
                    }
                    $contactIcq->setPersonId($person->getId());
                    $contactIcq->setValue($command->icq);
                    $contactIcq->setContactTypeCode(ContactType::CODE_ICQ);
                    $contactIcq->setCityId($command->cityId);
                    $em->persist($contactIcq);
                }
            } else {
                throw new NotFoundHttpException('Person not found');
            }
        }

        $em->flush();
    }
}