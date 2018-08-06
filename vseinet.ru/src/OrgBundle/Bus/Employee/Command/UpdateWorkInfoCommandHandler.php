<?php

namespace OrgBundle\Bus\Employee\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\ContactType;
use AppBundle\Entity\UserToSubrole;
use Doctrine\ORM\EntityNotFoundException;
use OrgBundle\Entity\Employee;
use OrgBundle\Entity\EmployeeToCashDesk;
use OrgBundle\Entity\EmployeeToGeoRoom;
use OrgBundle\Entity\OrgContact;

class UpdateWorkInfoCommandHandler extends MessageHandler
{
    public function handle(UpdateWorkInfoCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();


        /** @var Employee $employee */
        $employee = $em->getRepository(Employee::class)->find($command->id);

        if (!$employee)
            throw new EntityNotFoundException('Сотрудник не найден');


        // Select Subroles
        /** @var UserToSubrole[] $subroles */
        $subroles = $em->createQuery('
                SELECT usr
                FROM AppBundle:UserToSubrole AS usr
                WHERE usr.userId = :userId
                ORDER BY usr.subroleId
            ')
            ->setParameter('userId', $employee->getUserId())
            ->getResult();

        $newKeys = array_flip(array_unique($command->subrolesIds));
        $oldKeys = [];
        foreach ($subroles as $subrole) {
            if (!isset($newKeys[$subrole->getSubroleId()]))
                $em->remove($subrole);
            else
                $oldKeys[$subrole->getSubroleId()] = true;
        }
        foreach ($newKeys as $roleId => $role) {
            if (!isset($oldKeys[$roleId])) {
                $r = new UserToSubrole();
                $r->setUserId($employee->getUserId());
                $r->setSubroleId($roleId);
                $em->persist($r);
            }
        }


        // Select GeoRooms
        /** @var EmployeeToGeoRoom[] $georooms */
        $georooms = $em->createQuery('
                SELECT egr
                FROM OrgBundle:EmployeeToGeoRoom AS egr
                WHERE egr.employeeId = :userId
                ORDER BY egr.geoRoomId
            ')
            ->setParameter('userId', $employee->getUserId())
            ->getResult();

        $oldKeys = [];
        foreach ($georooms as $georoom) {
            if ($georoom->getGeoRoomId() != $command->geoRoomId)
                $em->remove($georoom);
            else
                $oldKeys[$georoom->getGeoRoomId()] = true;
        }
        if (!isset($oldKeys[$command->geoRoomId])) {
            $g = new EmployeeToGeoRoom();
            $g->setEmployeeId($employee->getUserId());
            $g->setGeoRoomId($command->geoRoomId);
            $g->setIsMain(true);
            $em->persist($g);
        }


        // Select Contacts
        /** @var OrgContact[] $contact */
        $contact = $em->createQuery('
                SELECT oc
                FROM OrgBundle:OrgContact AS oc
                WHERE oc.contactId = :contactId
            ')
            ->setParameter('contactId', $command->contactId)
            ->getResult();

        if ((count($contact) > 0) && ($contact[0]->getUserId() != $employee->getUserId())) {
            $contact = $contact[0];

            /** @var OrgContact[] $contacts */
            $contacts = $em->createQuery('
                    SELECT oc
                    FROM OrgBundle:OrgContact AS oc
                        INNER JOIN AppBundle:Contact AS c
                            WITH oc.contactId = c.id
                    WHERE oc.userId = :userId AND c.contactTypeCode = :contactType
                ')
                ->setParameter('userId', $employee->getUserId())
                ->setParameter('contactType', ContactType::CODE_PHONE)
                ->getResult();

            foreach ($contacts as $c) {
                $c->setUserId(null);
                $em->persist($c);
            }

            $contact->setUserId($employee->getUserId());
            $em->persist($contact);
        }


        // Select CashDesks
        /** @var EmployeeToCashDesk[] $cashDesks */
        $cashDesks = $em->createQuery('
                SELECT ecd
                FROM OrgBundle:EmployeeToCashDesk AS ecd
                WHERE ecd.employeeId = :userId
                ORDER BY ecd.cashDeskId
            ')
            ->setParameter('userId', $employee->getUserId())
            ->getResult();

        $newKeys = array_flip(array_unique($command->cashDeskIds));
        $oldKeys = [];
        $hasDefault = false;
        foreach ($cashDesks as $cashDesk) {
            if (!isset($newKeys[$cashDesk->getCashDeskId()]))
                $em->remove($cashDesk);
            else {
                $oldKeys[$cashDesk->getCashDeskId()] = true;
                if ($cashDesk->getIsDefault())
                    $hasDefault = true;
            }
        }
        foreach ($newKeys as $id => $pos) {
            if (!isset($oldKeys[$id])) {
                $cd = new EmployeeToCashDesk();
                $cd->setEmployeeId($employee->getUserId());
                $cd->setCashDeskId($id);

                if (!$hasDefault) {
                    $cd->setIsDefault(true);
                    $hasDefault = true;
                }

                $em->persist($cd);
            }
        }

        $em->flush();
    }
}