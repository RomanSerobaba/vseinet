<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Entity\User;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class BridgeUserAccountUpdateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setDescription('User account update.')
            ->setName('executor:user:account:update')
            ->addArgument('data', InputArgument::REQUIRED, 'Data?')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $data = json_decode($input->getArgument('data'), true);
        


        $em = $container->get('doctrine')->getManager();
        $user = $em->getRepository(User::class)->find($data['id']);
        if (!$user instanceof User) {
            $logger = new Logger('bridge');
            $this->logger->pushHandler(new StreamHandler($container->getParameter('kernel.logs_dir').'/bridge/bridge-'.date('Y-m-d').'.log', Logger::ERROR));
            $logger->error('User account update, user not found', ['userId' => $data['id']]);
        }





        $em->persist($user);
        $em->flush();
        $output->writeln('OK! Password are changed.');
    }
}