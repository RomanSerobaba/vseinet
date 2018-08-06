<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Exception\LogicException;

class BuildBusServicesConfigCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setDescription('Build bus services config.')
            ->setName('app:config:bus-services')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dir = $this->getContainer()->get('kernel')->getProjectDir();   
        
        $finder = new Finder();
        $finder->name('*Handler.php')->sortByName()->in($dir.DIRECTORY_SEPARATOR.'src');

        $config = ['services:'];
        $section = '';
        foreach ($finder as $fi) {
            if (3 != substr_count($fi->getRelativePath(), DIRECTORY_SEPARATOR)) {
                continue;
            }

            if ($section != $fi->getRelativePath()) {
                $section = $fi->getRelativePath();
                $type = str_replace([sprintf('%1$sBus%1$s', DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR], ':', $section);
                $config[] = "";
                $config[] = "    # {$type}";
            }
    
            $message = str_replace([DIRECTORY_SEPARATOR, 'Handler.php'], ['\\', ''], $fi->getRelativePathname());
            $handler = $message.'Handler';
            $name = str_replace(['\\Bus\\', '\\Command\\', '\\Query\\'], '_', $message);

            $config[] = "    {$name}:";
            $config[] = "        class: {$handler}";
            $config[] = "        public: true";
            $config[] = "        calls:";
            $config[] = "            - [ setContainer, [ '@service_container' ] ]";
            $config[] = "        tags:";

            switch (substr($message, -1)) {
                case 'd': // Command
                    $config[] = "            - { name: command_handler, handles: {$message} }";
                    break;

                case 'y': // Query
                    $config[] = "            - { name: query_handler, handles: {$message} }";
                    break;

                default:
                    throw new LogicException(sprintf('Class name of handler "%s" is not valid.', $handler));                    
            }
            $config[] = "";
        }

        $filesystem = new Filesystem();
        $filesystem->dumpFile($dir.sprintf('%1$sapp%1$sconfig%1$sbus-services.yml', DIRECTORY_SEPARATOR), implode("\n", $config)); 

        $output->writeln('<info>Config bus-services.yml is created.</info>');
    }
}