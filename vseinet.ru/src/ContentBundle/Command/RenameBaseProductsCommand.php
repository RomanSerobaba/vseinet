<?php

namespace ContentBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ContentBundle\Bus\BaseProduct\Command\RenameCommand;

class RenameBaseProductsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setDescription('Rename base products.')
            ->setName('executor:rename:base:products')
            ->addArgument('criteria', InputArgument::REQUIRED, 'Criteria?')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('command_bus')->handle(new RenameCommand(json_decode($input->getArgument('criteria'), true)));
        $output->writeln('<info>OK! Base products renamed successful.</info>');
    }
}