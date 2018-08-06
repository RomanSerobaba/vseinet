<?php

namespace ContentBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SMLPricelistLoadCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setDescription('Load SML pricelist.')
            ->setName('executor:sml:pricelist:load')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $uploadedQuantity = $this->getContainer()->get('sml.scraper')->grab();
        $output->writeln(sprintf('<info>OK! SML pricelist loaded %d products.</info>', $uploadedQuantity));
    }
}