<?php 

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Entity\User;
use AppBundle\Entity\Person;
use AppBundle\Entity\Contact;
use AppBundle\Entity\Role;
use AppBundle\Entity\Subrole;
use AppBundle\Entity\UserToSubrole;
use AppBundle\Enum\ContactTypeCode;
use AppBundle\Enum\UserRole;

class SiteBackendRegistrCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setDescription('Site backend registration for API access.')
            ->setName('site:backend:registr')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $em = $container->get('doctrine')->getManager();

        $person = $em->getRepository(Person::class)->findOneBy([
            'firstname' => 'site',
            'lastname' => 'backend',
        ]);
        if ($person instanceof Person) {
            $output->writeln('Site backend already registered');

            return;
        }

        $person = new Person();
        $person->setFirstname('site');
        $person->setLastname('backend');
        $em->persist($person);
        $em->flush();

        $encoder = $container->get('security.password_encoder');
        $password = $container->getParameter('site_backend_password');

        $user = new User();
        $user->setPersonId($person->getId());
        $user->setRegisteredAt(new \DateTime());
        $user->setPassword($encoder->encodePassword($user, $password));
        $em->persist($user);
        $em->flush();

        $role = $em->getRepository(Role::class)->findOneBy(['code' => UserRole::ADMIN]);
        $subrole = $em->getRepository(Subrole::class)->findOneBy(['roleId' => $role->getId()]);

        $u2sr = new UserToSubrole();
        $u2sr->setUserId($user->getId());
        $u2sr->setSubroleId($subrole->getId());
        $em->persist($u2sr);
 
        $contact = new Contact();
        $contact->setPersonId($person->getId());
        $contact->setValue('0000000000');
        $contact->setContactTypeCode(ContactTypeCode::MOBILE);
        $contact->setIsMain(true);
        $em->persist($contact);
        
        $em->flush();

        $output->writeln('Site backend was registered');
    }
}
