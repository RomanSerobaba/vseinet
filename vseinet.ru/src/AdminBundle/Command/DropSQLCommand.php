<?php

namespace AdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Exception\LogicException;

class DropSQLCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setDescription('Drop update SQL.')
            ->setName('update:drop:sql')
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
                if (0 === strpos(trim($sql), 'DROP')) {
                    $connection->query($sql);
                }
            }
        }

        $output->writeln('<info>Drop update SQl command was completed.</info>');
    }
}