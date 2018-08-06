<?php

namespace AppBundle\Bus\User\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\Query\ResultSetMapping as RSM;
use AppBundle\ORM\Query\DTORSM;
use AppBundle\Entity\User;
use AppBundle\Entity\UserData;
use AppBundle\Entity\Person;
use AppBundle\Entity\Contact;

class LoginCompleteCommandHandler extends MessageHandler
{
    public function handle(LoginCompleteCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository(User::class)->find($command->id);
        if (!$user instanceof User) {
            throw new NotFoundHttpException();
        }

        $user->person = $em->getRepository(Person::class)->find($user->getPersonId());
        if (!$user->person instanceof Person) {
            throw new NotFoundHttpException();
        }

        $user->contacts = $em->getRepository(Contact::class)->findBy(['personId' => $user->person->getId()]);

        $q = $em->createNativeQuery("
            SELECT 
                ar.code
            FROM user_to_acl_subrole AS u2asr 
            INNER JOIN acl_subrole AS asr ON asr.id = u2asr.acl_subrole_id 
            INNER JOIN acl_role AS ar ON ar.id = asr.acl_role_id 
            WHERE u2asr.user_id = :user_id   
        ", new RSM());
        $q->setParameter('user_id', $user->getId());
        $user->roles = $q->getResult('ListHydrator');
        
        if ($user->isEmployee()) {
            $q = $em->createNativeQuery("
                SELECT 
                    CASE WHEN EXISTS (
                        SELECT 1 
                        FROM org_employment_history 
                        WHERE org_employee_user_id = oe.user_id AND fired_at IS NULL
                    ) THEN false ELSE true END AS is_fired,
                    oe.clock_in_time,
                    r.ip AS ip_address
                FROM org_employee AS oe 
                INNER JOIN org_department AS od ON od.id = oe.org_department_id 
                INNER JOIN geo_room AS gr ON gr.id = od.geo_room_id 
                INNER JOIN representative AS r ON r.geo_point_id = gr.geo_point_id 
                WHERE oe.user_id = :user_id 
            ", new DTORSM(UserData::class, DTORSM::ARRAY_INDEX));
            $q->setParameter('user_id', $user->getId());
            $user->data = $q->getSingleResult('DTOHydrator');
        }

        $this->get('user.identity')->setUser($user);
    }
}
