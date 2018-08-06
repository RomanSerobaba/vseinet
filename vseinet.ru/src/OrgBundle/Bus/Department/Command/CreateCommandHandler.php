<?php 

namespace OrgBundle\Bus\Department\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityNotFoundException;
use OrgBundle\Entity\Department;
use OrgBundle\Entity\DepartmentToDepartment;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();


        $numberParts = [];
        if (!preg_match('/^((?:\d+\.)*)(\d+)$/', $command->number, $numberParts))
            throw new InvalidParameterException('Invalid format of number');

        $numberPar = trim($numberParts[1], " \t\r\n.");
        $numberVal = $numberParts[2] ?? 0;


        $queryPar = '
            SELECT d
            FROM OrgBundle:Department AS d
                INNER JOIN OrgBundle:DepartmentToDepartment AS dd
                    WITH d.id = dd.departmentId AND d.pid = dd.pid
                        AND (dd.activeSince IS NULL OR dd.activeSince <= CURRENT_TIMESTAMP())
                        AND (dd.activeTill IS NULL OR dd.activeTill >= CURRENT_TIMESTAMP())
            WHERE d.number';
        if ($numberPar) {
            $queryPar .= "='$numberPar'";
        } else {
            $queryPar .= ' IS NULL';
        }
        /** @var Department[] $parentNew */
        $parentNew = $em->createQuery($queryPar)->getResult();

        if (count($parentNew) != 1)
            throw new EntityNotFoundException('Destination department not found');

        $parentNew = $parentNew[0];


        $department = new Department();
        $department->setPid($parentNew->getId());
        $department->setName($command->name);
        $department->setNumber('');
        $department->setTypeCode($command->departmentTypeCode);
        $department->setSortOrder($numberVal > 0 ? $numberVal : 10000);

        $em->persist($department);
        $em->flush();


        /** @var DepartmentToDepartment[] $depToDep */
        $depToDep = $em->createQuery('
                    SELECT dd
                    FROM OrgBundle:DepartmentToDepartment AS dd
                    WHERE
                        dd.departmentId = :department_id AND dd.pid = :pid
                        AND (dd.activeSince IS NULL OR dd.activeSince <= CURRENT_TIMESTAMP())
                        AND (dd.activeTill IS NULL OR dd.activeTill >= CURRENT_TIMESTAMP())
                ')
            ->setParameter('department_id', $department->getId())
            ->setParameter('pid', $department->getPid())
            ->getResult();

        foreach ($depToDep as $dep) {
            $dep->setActivatedBy($currentUser->getId());
        }

        $em->flush();

        $this->get('uuid.manager')->saveId($command->uuid, $department->getId());
    }
}