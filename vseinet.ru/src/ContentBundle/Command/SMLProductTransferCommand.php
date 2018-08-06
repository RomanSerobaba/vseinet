<?php

namespace ContentBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SMLProductTransferCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setDescription('SML products transfering.')
            ->setName('executor:sml:product:transfer')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $transferQuantity = $this->getContainer()->get('sml.transfer')->transfer();
        $output->writeln(sprintf('<info>OK! SML transfered %d products.</info>', $transferQuantity));
    }
}