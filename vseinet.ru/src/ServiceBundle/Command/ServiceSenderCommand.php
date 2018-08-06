<?php

namespace ServiceBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ServiceBundle\Bus\Sender\Command\SendCommand;


class ServiceSenderCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setDescription('SMS, Viber and Email sender')
            ->setName('executor:service:sender')
            ->addArgument('type', InputArgument::REQUIRED, 'Message type')
            ->addArgument('data', InputArgument::REQUIRED, 'Message data');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $json = $input->getArgument('data');
        $data = json_decode($json, true);

        $this->getContainer()->get('command_bus')->handle(new SendCommand([
            'type' => strval($input->getArgument('type')),
            'data' => $data,
        ]));

        $output->writeln('<info>OK! Message successful sended</info>');
    }

}
