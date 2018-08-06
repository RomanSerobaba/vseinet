<?php

namespace OrgBundle\Bus\Work\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityNotFoundException;
use OrgBundle\Components\AttendanceComponent;

class GetAttendanceQueryHandler extends MessageHandler
{
    /**
     * @param GetAttendanceQuery $query
     * @return array
     * @throws EntityNotFoundException
     */
    public function handle(GetAttendanceQuery $query)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var User $currentUser */
        $currentUser = $this->get('user.identity')->getUser();

        $component = new AttendanceComponent($em, $currentUser);
        $attendance = $component->getAttendance($query->id, $query->since, $query->till, $query->collapse);

        if (!$attendance)
            throw new EntityNotFoundException('Сотрудник не найден');

        return $attendance;
    }
}