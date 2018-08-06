<?php 

namespace SupplyBundle\Bus\Suppliers\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\Contact;
use AppBundle\Entity\Person;
use SupplyBundle\Entity\Supplier;
use AccountingBundle\Entity\Counteragent;
use SupplyBundle\Entity\SupplierEmployee;
use SupplyBundle\Entity\SupplierToCounteragent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SaveSupplierCommandHandler extends MessageHandler
{
    public function handle(SaveSupplierCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        if (!empty($command->id)) { //edit
            $model = $em->getRepository(Supplier::class)->findOneBy(['id' => $command->id,]);

            if (!$model instanceof Supplier) {
                throw new NotFoundHttpException('Supplier not found');
            }
        } else { //new
            $model = new Supplier();
        }

        $model->setName($command->name);
        $model->setCode($command->code);
        $model->setManagerId($command->managerId);
        $model->setHasFreeDelivery($command->hasFreeDelivery);
        $model->setIsActive($command->isActive);
        $model->setContractTill($command->contractTill);
        $model->setSiteUrl($command->siteUrl);
        $model->setDescription($command->description);
        $model->setAuthUrl($command->authUrl);
        $model->setAuthLogin($command->authLogin);
        $model->setAuthPassword($command->authPassword);
        $model->setAuthComment($command->authComment);

        $em->persist($model);
        $em->flush();

        if (!$model->getId()) {
            throw new \Exception('Ошибка сохранения поставщика');
        }

        /**
         * Юр. лицо
         */
        $counteragent = $em->getRepository(Counteragent::class)->findOneBy(['tin' => $command->tin,]);
        if (empty($counteragent)) {
            $counteragent = new Counteragent();

            $counteragent->setName($command->counteragentName);
            $counteragent->setTin($command->tin);
            $counteragent->setKpp($command->kpp);
            $counteragent->setOgrn($command->ogrn);
            $counteragent->setOkpo($command->okpo);
            $counteragent->setVatRate($command->vatRate);

            $em->persist($counteragent);
            $em->flush();
        }

        if ($counteragent->getId()) {
            $isLinkExist = (bool) $em->getRepository(SupplierToCounteragent::class)->count([
                'supplier_id' => $model->getId(),
                'counteragent_id' => $counteragent->getId(),
                ]
            );

            if (!$isLinkExist) {
                $linksCount = $em->getRepository(SupplierToCounteragent::class)->count(['supplier_id' => $model->getId(),]);

                $link = new SupplierToCounteragent();
                $link->setSupplierId($model->getId());
                $link->setCounteragentId($counteragent->getId());
                $link->setIsActive(empty($linksCount));
                $link->setIsMain(empty($linksCount));

                $em->persist($link);
                $em->flush();
            }
        }

        /*
         * Контакты
         */
        $existsContacts = [];
        $q = $em->createQuery('
            SELECT 
                DISTINCT person.id as person_id,
                person.firstname,
                person.secondname,
                person.lastname,
                supplier_employee.id
            FROM
                supplier_employee
                INNER JOIN person ON person.id = supplier_employee.person_id
            WHERE
                supplier_id = :supplier_id
        ');
        $q->setParameter('supplier_id', $model->getId());

        $rows = $q->getArrayResult();
        foreach ($rows as $row) {
            $row['contacts'] = [];

            $q = $em->createQuery('
                SELECT 
                    id,
                    contact_type_code,
                    value,
                    short_value,
                    comment
                FROM
                    contact
                WHERE
                    person_id = :person_id
            ');
            $q->setParameter('person_id', $row['person_id']);

            $contactRows = $q->getArrayResult();
            foreach ($contactRows as $contactRow) {
                $row['contacts'][$contactRow['id']] = $contactRow;
            }

            $existsContacts[$row['person_id']] = $row;
        }

        if (!empty($command->contacts)) {
            foreach ($command->contacts as $contact) {
                if (empty($contact['person_id'])) { //new
                    $personModel = new Person();
                    $personModel->setFirstname($contact['firstname']);
                    $personModel->setSecondname($contact['secondname']);
                    $personModel->setLastname($contact['lastname']);

                    $em->persist($personModel);
                    $em->flush();

                    if ($personModel->getId()) {
                        if (!empty($contact['phone'])) {
                            $this->_addContact($em, $personModel->getId(), Contact::CONTACT_TYPE_CODE_PHONE, $contact['phone'], $contact['comment']);
                        }
                        if (!empty($contact['email'])) {
                            $this->_addContact($em, $personModel->getId(), Contact::CONTACT_TYPE_CODE_EMAIL, $contact['email'], $contact['comment']);
                        }
                        if (!empty($contact['skype'])) {
                            $this->_addContact($em, $personModel->getId(), Contact::CONTACT_TYPE_CODE_SKYPE, $contact['skype'], $contact['comment']);
                        }
                    }

                    $supplierEmployeeModel = new SupplierEmployee();
                    $supplierEmployeeModel->setPersonId($personModel->getId());
                    $supplierEmployeeModel->setSupplierId($model->getId());
                    $supplierEmployeeModel->setPosition($contact['position']);

                    $em->persist($supplierEmployeeModel);
                    $em->flush();
                } else { //edit
                    $personModel = $em->getRepository(Person::class)->findOneBy(['id' => $contact['person_id'],]);
                    if ($personModel instanceof Person) {
                        $personModel->setFirstname($contact['firstname']);
                        $personModel->setSecondname($contact['secondname']);
                        $personModel->setLastname($contact['lastname']);

                        $em->persist($personModel);
                        $em->flush();

                        if (!empty($contact['phone'])) {
                            $this->_updateContact($em, $personModel->getId(), Contact::CONTACT_TYPE_CODE_PHONE, $contact['phone'], $contact['comment']);
                        }
                        if (!empty($contact['email'])) {
                            $this->_updateContact($em, $personModel->getId(), Contact::CONTACT_TYPE_CODE_EMAIL, $contact['email'], $contact['comment']);
                        }
                        if (!empty($contact['skype'])) {
                            $this->_updateContact($em, $personModel->getId(), Contact::CONTACT_TYPE_CODE_SKYPE, $contact['skype'], $contact['comment']);
                        }

                        $supplierEmployeeModel = $em->getRepository(SupplierEmployee::class)->findOneBy(['supplier_id' => $model->getId(), 'person_id' => $contact['person_id'],]);
                        if ($supplierEmployeeModel instanceof SupplierEmployee) {
                            $supplierEmployeeModel->setPosition($contact['position']);

                            $em->persist($supplierEmployeeModel);
                            $em->flush();
                        }
                    }
                }
            }
        } else {
            if (!empty($existsContacts)) {
                $query = $em->createQuery("
                    DELETE FROM supplier_employee WHERE supplier_id = :supplier_id
                ");
                $query->setParameter('supplier_id', $model->getId());
                $query->execute();
            }
        }
    }

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param int                         $personID
     * @param string                      $type
     * @param string                      $value
     * @param string                      $comment
     *
     * @return int
     */
    private function _addContact(\Doctrine\ORM\EntityManager $em, int $personID, string $type, string $value, string $comment) : int
    {
        $contactModel = new Contact();
        $contactModel->setContactTypeCode($type);
        $contactModel->setValue($value);
        $contactModel->setComment($comment);
        $contactModel->setPersonId($personID);

        $em->persist($contactModel);
        $em->flush();

        return $contactModel->getId();
    }

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param int                         $personID
     * @param string                      $type
     * @param string                      $value
     * @param string                      $comment
     *
     * @return int
     */
    private function _updateContact(\Doctrine\ORM\EntityManager $em, int $personID, string $type, string $value, string $comment) : int
    {
        $contactModel = $this->_getContactByPersonType($em, $personID, $type);

        if ($contactModel instanceof Contact) {
            $contactModel->setValue($value);
            $contactModel->setComment($comment);

            $em->persist($contactModel);
            $em->flush();
        } else {
            $this->_addContact($em, $personID, $type, $value, $comment);
        }

        return $contactModel->getId();
    }

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param int                         $personID
     * @param string                      $type
     *
     * @return Contact
     */
    private function _getContactByPersonType(\Doctrine\ORM\EntityManager $em, int $personID, string $type) : Contact
    {
        return $em->getRepository(Contact::class)->findOneBy(['person_id' => $personID, 'contact_type_code' => $type,]);
    }
}