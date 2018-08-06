<?php

namespace OrgBundle\Components;

use AppBundle\Entity\ContactType;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use OrgBundle\Bus\Employee\Query\DTO;

class EmployeeComponent
{
    /**
     * Entity Manager
     * @var EntityManager
     */
    protected $em;

    /**
     * Current user
     * @var User $currentUser
     */
    protected $user;

    /**
     * Cached results
     * @var DTO\EmployeeInfo[][]
     */
    private static $results;

    /**
     * AttendanceComponent constructor.
     * @param EntityManager $em
     * @param User $user
     */
    public function __construct(EntityManager $em, User $user=null)
    {
        $this->em = $em;
        $this->user = $user;
    }

    /**
     * @param int $userId
     * @param bool $allInfo
     * @return DTO\EmployeeInfo
     */
    public function getInfo(int $userId=null, bool $allInfo=false)
    {
        if (!$userId && $this->user)
            $userId = $this->user->getId();

        if (!isset(self::$results[$userId][0])) {
            $employees = $this->em->createQuery('
                    SELECT
                        NEW OrgBundle\Bus\Employee\Query\DTO\EmployeeInfo (
                            em.userId,
                            ed.departmentId,
                            dd.number,
                            dd.name,
                            TRIM(CONCAT(
                                COALESCE(CONCAT(pp.lastname, \' \'), \'\'),
                                COALESCE(CONCAT(pp.firstname, \' \'), \'\'),
                                COALESCE(pp.secondname, \'\')
                            )),
                            em.sortOrder,
                            em.position,
                            eh.hiredAt,
                            eh.firedAt,
                            CASE
                                WHEN ed.activeSince IS NOT NULL
                                THEN TRUE
                                ELSE FALSE
                            END,
                            em.workingHoursWeekly,
                            em.clockInTime,
                            es.isIrregular
                        ) AS info,
                        es.s1, es.t1,
                        es.s2, es.t2,
                        es.s3, es.t3,
                        es.s4, es.t4,
                        es.s5, es.t5,
                        es.s6, es.t6,
                        es.s7, es.t7
                    FROM
                        OrgBundle:Employee AS em
                        INNER JOIN OrgBundle:EmploymentHistory AS eh
                            WITH em.userId = eh.employeeUserId
                                AND (eh.firedAt IS NULL OR eh.firedAt >= CURRENT_TIMESTAMP())
                        LEFT JOIN OrgBundle:EmployeeToDepartment AS ed
                            WITH ed.employeeUserId = em.userId AND ed.isSynthetic = false
                                AND (ed.activeSince IS NULL OR ed.activeSince <= CURRENT_TIMESTAMP())
                                AND (ed.activeTill IS NULL OR ed.activeTill >= CURRENT_TIMESTAMP())
                        LEFT JOIN OrgBundle:Department AS dd
                            WITH ed.departmentId = dd.id
                        LEFT JOIN AppBundle:User AS uu
                            WITH em.userId = uu.id
                        LEFT JOIN AppBundle:Person AS pp
                            WITH uu.personId = pp.id
                        LEFT JOIN OrgBundle:EmployeeSchedule AS es
                            WITH em.userId = es.employeeUserId
                                AND es.activeSince <= CURRENT_TIMESTAMP()
                                AND (es.activeTill IS NULL OR es.activeTill >= CURRENT_TIMESTAMP())
                    WHERE em.userId = :userId
                ')
                ->setParameter('userId', $userId)
                ->getResult();

            if (count($employees) <= 0) {
                self::$results[$userId][0] = null;
                self::$results[$userId][1] = null;
                return null;
            }


            /** @var DTO\EmployeeInfo $employee */
            $employee = $employees[0]['info'];

            $weekDay = date('N');

            $employee->workSince = $employees[0]["s$weekDay"];
            $employee->workTill = $employees[0]["t$weekDay"];


            // Select Subroles
            /** @var DTO\SubroleInfo[] $subroles */
            $subroles = $this->em->createQuery('
                    SELECT
                        NEW OrgBundle\Bus\Employee\Query\DTO\SubroleInfo (
                            asr.id,
                            ar.id,
                            ar.name,
                            ar.code,
                            ar.sortOrder,
                            asr.grade
                        )
                    FROM AppBundle:Subrole AS asr
                        INNER JOIN AppBundle:Role AS ar
                            WITH asr.roleId = ar.id
                        INNER JOIN AppBundle:UserToSubrole AS uts
                            WITH asr.id = uts.subroleId
                    WHERE uts.userId = :userId
                    ORDER BY ar.sortOrder, asr.grade
                ')
                ->setParameter('userId', $employee->userId)
                ->getResult();
            $employee->subroles = $subroles;

            self::$results[$userId][0] = $employee;
        }
        $employee = self::$results[$userId][0];


        if ($allInfo) {
            if (!isset(self::$results[$userId][1])) {
                // Select GeoRooms
                /** @var DTO\GeoRoomInfo[] $georoom */
                $georoom = $this->em->createQuery('
                        SELECT
                            NEW OrgBundle\Bus\Employee\Query\DTO\GeoRoomInfo (
                                oetgr.geoRoomId,
                                oetgr.isMain,
                                oetgr.isAccountable,
                                gr.type,
                                gr.name,
                                gr.code,
                                gr.geoPointId,
                                gp.type,
                                gp.name,
                                gp.code,
                                gp.geoAddressId
                            )
                        FROM OrgBundle:EmployeeToGeoRoom AS oetgr
                            INNER JOIN OrgBundle:GeoRoom AS gr
                                WITH oetgr.geoRoomId = gr.id
                            LEFT JOIN OrgBundle:GeoPoint AS gp
                                WITH gr.geoPointId = gp.id
                        WHERE oetgr.employeeId = :userId
                        ORDER BY oetgr.isMain
                    ')
                    ->setParameter('userId', $employee->userId)
                    ->getResult();

                if (count($georoom) > 0) {
                    $georoom = $georoom[0];

                    if ($georoom->geoAddressId > 0) {
                        /** @var \AppBundle\Bus\User\Query\DTO\Address[] $address */
                        $address = $this->em->createQuery('
                                SELECT
                                    NEW AppBundle\Bus\User\Query\DTO\Address (
                                        a.id,
                                        a.geoStreetId,
                                        a.house,
                                        a.building,
                                        a.apartment,
                                        a.floor,
                                        a.hasLift,
                                        a.office,
                                        a.geoSubwayStationId,
                                        a.coordinates,
                                        a.comment,
                                        a.address
                                    )
                                FROM ThirdPartyBundle:GeoAddress AS a
                                WHERE a.id = :addressId
                            ')
                            ->setParameter('addressId', $georoom->geoAddressId)
                            ->getResult();

                        if (count($address) > 0) {
                            $address = $address[0];

                            if ($address->geoStreetId > 0) {
                                /** @var \GeoBundle\Service\DTO\StreetInfo[] $street */
                                $street = $this->em->createQuery('
                                        SELECT
                                            NEW GeoBundle\Service\DTO\StreetInfo (
                                                gregion.id,
                                                gregion.name,
                                                gregion.unit,
                                                gregion.AOGUID,
                        
                                                gcity.id,
                                                gcity.name,
                                                gcity.unit,
                                                gcity.isCentral,
                                                gcity.phoneCode,
                                                gcity.AOGUID,
                        
                                                gstreet.id,
                                                gstreet.name,
                                                gstreet.unit,
                                                gstreet.AOGUID
                                            )
                                        FROM ThirdPartyBundle:GeoStreet AS gstreet
                                            LEFT JOIN ThirdPartyBundle:GeoCity AS gcity
                                                WITH gstreet.geoCityId = gcity.id
                                            LEFT JOIN ThirdPartyBundle:GeoRegion AS gregion
                                                WITH gcity.geoRegionId = gregion.id
                                        WHERE gstreet.id = :streetId
                                    ')
                                    ->setParameter('streetId', $address->geoStreetId)
                                    ->getResult();

                                if (count($street) > 0)
                                    $address->geoStreet = $street[0];
                            }

                            $georoom->address = $address;
                        }
                    }

                    $employee->geoRoom = $georoom;
                }


                // Select Contacts
                /** @var DTO\ContactInfo[] $contact */
                $contact = $this->em->createQuery('
                        SELECT
                            NEW OrgBundle\Bus\Employee\Query\DTO\ContactInfo (
                                oc.contactId,
                                oc.departmentId,
                                c.contactTypeCode,
                                c.value,
                                c.isMain
                            )
                        FROM OrgBundle:OrgContact AS oc
                            INNER JOIN AppBundle:Contact AS c
                                WITH oc.contactId = c.id
                        WHERE oc.userId = :userId
                            AND c.contactTypeCode = :contactType
                    ')
                    ->setParameter('userId', $employee->userId)
                    ->setParameter('contactType', ContactType::CODE_PHONE)
                    ->getResult();

                if (count($contact) > 0)
                    $employee->contact = $contact[0];


                // Select CashDesks
                /** @var DTO\CashDeskInfo[] $cashDesks */
                $cashDesks = $this->em->createQuery('
                        SELECT
                            oetcd.cashDeskId,
                            oetcd.isDefault,
                            cd.title,
                            cd.departmentId,
                            d.number,
                            d.name,
                            cd.geoRoomId,
                            gr.type,
                            gr.name
                        FROM OrgBundle:EmployeeToCashDesk AS oetcd
                            INNER JOIN OrgBundle:CashDesk AS cd
                                WITH oetcd.cashDeskId = cd.id
                            LEFT JOIN OrgBundle:Department AS d
                                WITH cd.departmentId = d.id
                            LEFT JOIN OrgBundle:GeoRoom AS gr
                                WITH cd.geoRoomId = gr.id
                        WHERE oetcd.employeeId = :userId
                        ORDER BY oetcd.cashDeskId
                    ')
                    ->setParameter('userId', $employee->userId)
                    ->getResult();

                $employee->cashDesks = $cashDesks;

                self::$results[$userId][1] = $employee;
            }

            $employee = self::$results[$userId][1];
        }

        return $employee;
    }

    /**
     * @param int $userId
     * @return bool
     */
    public function checkAdmin(int $userId)
    {
        $employee = $this->getInfo($userId);
        if (!$employee)  // Нет сотрудника - нет доступа
            return false;

        foreach ($employee->subroles as $subrole) {
            if ($subrole->code == 'ADMIN')  // Одна из ролей Админ - доступ разрешен
                return true;
        }

        return false;
    }

    /**
     * @param int $userId
     * @param int $departmentId
     * @return bool
     */
    public function checkChiefFor(int $userId, int $departmentId)
    {
        $employee = $this->getInfo($userId);
        if (!$employee)  // Нет сотрудника - нет доступа
            return false;

        if ($this->checkAdmin($userId))
            return true;

        if ($employee->sortOrder > 1)
            return false;  // Так этот сотрудник не начальник даже

        $departments = $this->em->createQuery('
                SELECT
                    dp.plevel,
                    ed.departmentId,
                    ed.employeeUserId
                FROM OrgBundle:DepartmentPath AS dp
                    INNER JOIN OrgBundle:EmployeeToDepartment AS ed
                        WITH dp.pid = ed.departmentId
                            AND ed.activeSince <= CURRENT_TIMESTAMP()
                            AND (ed.activeTill IS NULL OR ed.activeTill >= CURRENT_TIMESTAMP())
                WHERE dp.departmentId = :departmentId AND ed.employeeUserId = :employeeId
                ORDER BY dp.plevel
            ')
            ->setParameter('departmentId', $departmentId)
            ->setParameter('employeeId', $userId)
            ->getArrayResult();

        if ($departments && count($departments) > 0)
            return true;

        return false;
    }
}