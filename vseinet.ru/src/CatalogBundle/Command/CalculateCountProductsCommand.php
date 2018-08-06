<?php

namespace CatalogBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use CatalogBundle\Bus\Category\Command\CalcCountProductsCommand;

class CalculateCountProductsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setDescription('Calculate count products in category.')
            ->setName('catalog:calculate:count-products')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('command_bus')->handle(new CalcCountProductsCommand());

        $output->writeln('<info>Calculation was successful.</info>');
    }
}
