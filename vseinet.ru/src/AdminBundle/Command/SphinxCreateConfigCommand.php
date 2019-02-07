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
            ->setDescription('Sphinx create config.')
            ->setName('sphinx:create:config')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $connection = $container->get('doctrine')->getManager()->getConnection();
        $templating = $container->get('templating');
        $filesystem = new Filesystem();

        $geoCityIds = $connection->query("
            SELECT gp.geo_city_id AS id
            FROM representative AS r
            INNER JOIN geo_point AS gp ON gp.id = r.geo_point_id
            WHERE r.is_active = true AND r.has_retail = true AND r.type IN ('our', 'torg', 'partner')
            GROUP BY gp.geo_city_id
        ")->fetchAll(\PDO::FETCH_COLUMN);

        $indexer = $templating->render('Sphinx/indexer.js.twig',[
            'geo_city_ids' => $geoCityIds,
        ]);

        $filesystem->dumpFile($container->getParameter('sphinx.indexer.path'), $indexer);

        $productIndexes = [];
        foreach (array_merge([0], $geoCityIds) as $geoCityId) {
            $productQuery = $templating->render('Sphinx/product_query.sql.twig', [
                'geo_city_id' => $geoCityId,
            ]);
            $productIndexes[] = $templating->render('Sphinx/product_index.html.twig', [
                'geo_city_id' => $geoCityId,
                'product_query' => preg_replace('/\s+/', ' ', implode(' ', explode("\n", $productQuery))),
            ]);
        }

        $config = $templating->render('Sphinx/config.html.twig', [
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
        ]);

        $filesystem->dumpFile($container->getParameter('sphinx.conf.path'), $config);

        $output->writeln('<info>Sphinx config created successful.</info>');
    }
}