<?php

namespace AppBundle\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\User;
use AppBundle\Entity\Person;
use AppBundle\Entity\FinancialCounteragent;
use AppBundle\Entity\Contact;
use AppBundle\Enum\ContactTypeCode;
use AppBundle\Enum\UserRole;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserProvider implements UserProviderInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username): UserInterface
    {
        if (false === strpos($username, '@')) {
            $phone = preg_replace('/\D+/', '', $username);
            if (11 === strlen($phone)) {
                $phone = substr($phone, 1);
            }
            $contact = $this->em->getRepository(Contact::class)->findOneBy([
                'contactTypeCode' => ContactTypeCode::MOBILE,
                'value' => $phone,
                'isMain' => true,
            ]);
            if (!$contact instanceof Contact) {
                throw new UsernameNotFoundException(sprintf('Пользователь с мобильным телефоном %s не найден', $phone));
            }
        } else {
            $q = $this->em->createQuery('
                SELECT c
                FROM AppBundle:Contact AS c
                WHERE c.contactTypeCode = :contactTypeCode_EMAIL AND c.isMain = TRUE AND LOWER(c.value) = LOWER(:value)
            ');
            $q->setParameters([
                'contactTypeCode_EMAIL' => ContactTypeCode::EMAIL,
                'value' => $username,
            ]);
            $contact = $q->getOneOrNullResult();

            if (!$contact instanceof Contact) {
                throw new UsernameNotFoundException(sprintf('Пользователь с email %s не найден', $username));
            }
        }

        $user = $this->em->getRepository(User::class)->findOneBy(['personId' => $contact->getPersonId()]);
        if (!$user instanceof User) {
            throw new UsernameNotFoundException('Пользователь не найден');
        }
        $user->setUsername($contact->getValue());

        $stmt = $this->em->getConnection()->prepare("
            SELECT
                'ROLE_' || ar.code
            FROM user_to_acl_subrole AS u2asr
            INNER JOIN acl_subrole AS asr ON asr.id = u2asr.acl_subrole_id
            INNER JOIN acl_role AS ar ON ar.id = asr.acl_role_id
            WHERE u2asr.user_id = :user_id
        ");
        $stmt->execute(['user_id' => $user->getId()]);
        $user->roles = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        if (!array_intersect([UserRole::CLIENT, UserRole::FRANCHISER, UserRole::WHOLESALER, UserRole::FREELANCER], $user->roles)) {
            $user->roles[] = UserRole::EMPLOYEE;
        }

        return $this->refreshUser($user);
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        if (null === $user->person) {
            $user->person = $this->em->getRepository(Person::class)->find($user->getPersonId());
            $user->financialCounteragent = $this->em->getRepository(FinancialCounteragent::class)->findOneBy(['userId' => $user->getId()]);
        }

        if ($user->isEmployee()) {
            $stmt = $this->em->getConnection()->prepare('
                SELECT
                    CASE WHEN EXISTS (
                        SELECT 1
                        FROM org_employment_history
                        WHERE org_employee_user_id = oe.user_id AND fired_at IS NULL
                    ) THEN false ELSE true END AS is_fired,
                    oe.clock_in_time,
                    r.ip
                FROM org_employee AS oe
                INNER JOIN org_employee_to_geo_room AS oe2gr ON oe.user_id = oe2gr.org_employee_user_id
                INNER JOIN geo_room AS gr ON gr.id = oe2gr.geo_room_id
                INNER JOIN representative AS r ON r.geo_point_id = gr.geo_point_id
                WHERE oe.user_id = :user_id AND oe2gr.is_main = true
            ');
            $stmt->execute(['user_id' => $user->getId()]);
            $data = $stmt->fetch(\PDO::FETCH_ASSOC);
            $user->isFired = (bool) $data['is_fired'];
            $user->clockInTime = $data['clock_in_time'] ? new \DateTime($data['clock_in_time']) : null;
            $user->ipAddress = $data['ip'];

            $stmt = $this->em->getConnection()->prepare('
                SELECT
                    r.*,
                    er.is_main,
                    er.is_accountable
                FROM org_employee_to_geo_room AS er
                INNER JOIN geo_room AS r ON r.id = er.geo_room_id
                WHERE er.org_employee_user_id = :user_id
                ORDER BY er.is_main DESC
            ');
            $stmt->execute(['user_id' => $user->getId()]);
            $user->geoRooms = $stmt->fetchAll();

            if (count($user->geoRooms) > 0) {
                $defaultGeoRoom = reset($user->geoRooms);
                $user->defaultGeoPointId = $defaultGeoRoom['geo_point_id'];
                $user->defaultGeoRoomId = $defaultGeoRoom['id'];
            }
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class): bool
    {
        return User::class === $class;
    }

    /**
     * Returns container parameter.
     *
     * @return mixin
     */
    public function getParameter($parameter, $default = null)
    {
        return $this->container->hasParameter($parameter) ? $this->container->getParameter($parameter) : $default;
    }
}
