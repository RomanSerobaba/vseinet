<?php

namespace ContentBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ContentBundle\Bus\Statistics\Command\FillnessRefreshCommand;

class ContentFillnessRefreshCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setDescription('Content fillness refreshing.')
            ->setName('executor:content:fillness:refresh')
            ->addArgument('subject', InputArgument::REQUIRED, 'Subject?')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('command_bus')->handle(new FillnessRefreshCommand(['subject' => $input->getArgument('subject')]));

        $output->writeln('<info>OK! Content fillness refresh successful.</info>');        
    }
}