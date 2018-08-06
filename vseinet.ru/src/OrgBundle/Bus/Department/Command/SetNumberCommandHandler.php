<?php 

namespace OrgBundle\Bus\Department\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityNotFoundException;
use OrgBundle\Entity\Department;
use OrgBundle\Entity\DepartmentToDepartment;
use OrgBundle\Repository\DepartmentRepository;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Validator\Exception\RuntimeException;

class SetNumberCommandHandler extends MessageHandler
{
    public function handle(SetNumberCommand $command)
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
        if (!preg_match('/^((?:\d+\.)*)(\d+)$/', $command->value, $numberParts))
            throw new InvalidParameterException('Invalid format of number');

        $numberPar = trim($numberParts[1], " \t\r\n.");
        $numberVal = $numberParts[2] ?? 0;


        /** @var DepartmentRepository $departmentRep */
        $departmentRep = $em->getRepository(Department::class);

        /** @var Department $department */
        $department = $departmentRep->find($command->id);

        if (!$department)
            throw new EntityNotFoundException('Department not found');

        if ($department->getNumber() && (substr($command->value, 0, strlen($department->getNumber())) == $department->getNumber()))
            throw new RuntimeException('It is impossible to move the department inside self');


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


        if ($department->getPid() != $parentNew->getId()) {
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

            $departmentIsActive = false;
            foreach ($depToDep as $dep) {
                if ($dep->getActiveSince())
                    $departmentIsActive = true;

                $dep->setActiveTill(new \DateTime());
            }

            $department->setPid($parentNew->getId());

            $depInDep = new DepartmentToDepartment();
            $depInDep->setDepartmentId($department->getId());
            $depInDep->setPid($department->getPid());
            $depInDep->setActivatedBy($currentUser->getId());

            if ($departmentIsActive)
                $depInDep->setActiveSince(new \DateTime());

            $em->persist($depInDep);
        }

        $department->setSortOrder($numberVal > 0 ? $numberVal : 10000);

        $em->persist($department);
        $em->flush();
    }
}