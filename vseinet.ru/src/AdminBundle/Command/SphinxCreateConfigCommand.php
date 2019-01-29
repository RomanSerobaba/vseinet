<?php

namespace AdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class SphinxCreateConfigCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setDescription('Manticore create config.')
            ->setName('manticore:create:config')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $connection = $container->get('doctrine')->getManager()->getConnection();
        $templating = $container->get('templating');

        $geoCities = $connection->query("
            SELECT gp.geo_city_id AS id
            FROM representative AS r
            INNER JOIN geo_point AS gp ON gp.id = r.geo_point_id
            WHERE r.is_active = true AND r.has_retail = true AND r.type IN ('our', 'torg', 'partner')
            GROUP BY gp.geo_city_id
            UNION
            SELECT 0 AS id
        ");

        $workdir = '/var/lib/manticore/';
        $pidfile = '/var/run/searchd.pid';

        $productIndexes = [];
        foreach ($geoCities as $geoCity) {
            $productQuery = $templating->render('Sphinx/product_query.sql.twig', [
                'geo_city_id' => $geoCity['id'],
            ]);
            $productIndexes[] = $templating->render('Sphinx/product_index.html.twig', [
                'geo_city_id' => $geoCity['id'],
                'product_query' => preg_replace('/\s+/', ' ', implode(' ', explode("\n", $productQuery))),
                'workdir' => $workdir,
            ]);
        }

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
            'product_indexes' => $productIndexes,
            'workdir' => $workdir,
            'pidfile' => $pidfile,
        ]);

        $configPath = $container->getParameter('sphinx.conf.path');

        $filesystem = new Filesystem();
        $filesystem->dumpFile($configPath, $config);

        $output->writeln(sprintf('<info>Manticore config (%s) created successful.</info>', $configPath));
    }
}