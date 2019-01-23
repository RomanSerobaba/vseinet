<?php

namespace AdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class ExecSQLCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setDescription('Execute update SQL.')
            ->setName('update:exec:sql')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = $this->getContainer()->get('doctrine')->getManager()->getConnection();

        $finder = new Finder();
        $finder->name('*.sql')->sortByName()->in(dirname(__DIR__).DIRECTORY_SEPARATOR.'SQL');
        foreach ($finder as $fi) {
            $content = $fi->getContents();
            foreach (explode('-- #', $content) as $sql) {
                $connection->query($sql);
            }
        }

        $output->writeln('<info>Execute update SQl command was completed.</info>');
    }
}
