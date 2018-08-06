<?php

namespace ContentBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use ContentBundle\Bus\SupplierPricelist\Command\LoadCommand;

class SupplierPricelistLoadCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setDescription('Load supplier pricelist.')
            ->setName('executor:supplier:pricelist:load')
            ->addArgument('id', InputArgument::REQUIRED, 'Pricelist id?')
            ->addArgument('filename', InputArgument::REQUIRED, 'Pricelist filename?')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $filename = $this->getContainer()->getParameter('supplier.pricelist.path').'/'.$input->getArgument('filename');
            $file = new File($filename);
            $this->getContainer()->get('command_bus')->handle(new LoadCommand([
                'id' => intval($input->getArgument('id')),
                'filename' => $file->getPathname(),
            ]));      
            $output->writeln('<info>OK! Supplier pricelist loaded successful.</info>');        
        }
        catch (FileNotFoundException $e) {
            $output->writeLn(sprintf('<error>ERROR! Supplier pricelist  `%s` not found.</error>', $filename));
        }
    }
}