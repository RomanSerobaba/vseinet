<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Entity\User;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class BridgeUserChangePasswordCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setDescription('User change password.')
            ->setName('executor:user:change:password')
            ->addArgument('data', InputArgument::REQUIRED, 'Data?')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $data = json_decode($input->getArgument('data'), true);
        $container = $this->getContainer();
        $em = $container->get('doctrine')->getManager();
        $user = $em->getRepository(User::class)->find($data['id']);
        if (!$user instanceof User) {
            $logger = new Logger('bridge');
            $this->logger->pushHandler(new StreamHandler($container->getParameter('kernel.logs_dir').'/bridge/bridge-'.date('Y-m-d').'.log', Logger::ERROR));
            $logger->error('UserChangePassword, User not found', ['userId' => $data['id']]);
        }
        $user->setPassword(password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 4]));
        $em->persist($user);
        $em->flush();
        $output->writeln('OK! Password are changed.');
    }
}