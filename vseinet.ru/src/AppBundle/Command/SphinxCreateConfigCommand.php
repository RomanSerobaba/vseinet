<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class SphinxCreateConfigCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setDescription('Sphinx create config.')
            ->setName('sphinx:create:config')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $config = $container->get('templating')->render('Sphinx/config.html.twig', [
            'pgsql' => [
                'host' => $container->getParameter('pgsql_host'),
                'port' => $container->getParameter('pgsql_port'),
                'dbname' => $container->getParameter('pgsql_dbname'),
                'user' => $container->getParameter('pgsql_user'),
                'password' => $container->getParameter('pgsql_password'),
            ],
            'sphinxql' => [
                'port' => $container->getParameter('sphinxql_port'),
            ],
        ]);

        $filesystem = new Filesystem();
        $filesystem->dumpFile($container->getParameter('sphinx.conf.path'), $config);

        $output->writeln('<info>Sphinx config created successful.</info>');
    }
}