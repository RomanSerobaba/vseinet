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
        $finder->name('*CommandHandler.php')->name('*QueryHandler.php')->sortByName()->in($dir.DIRECTORY_SEPARATOR.'src');

        $config[] = 'services:';
        $section = '';
        foreach ($finder as $fi) {
            if ('MessageHandler' === $fi->getBasename('.php')) {
                continue;
            }

            if ($section != $fi->getRelativePath()) {
                $section = $fi->getRelativePath();
                $config[] = '';
                $config[] = sprintf('    # %s', str_replace(DIRECTORY_SEPARATOR, ':', $section));
            }

            $message = str_replace([DIRECTORY_SEPARATOR, 'Handler.php'], ["\\", ''], $fi->getRelativePathname());
            $handler = $message.'Handler';

            $config[] = sprintf('    %s:', str_replace("\\", '_', $message));
            $config[] = sprintf('        class: %s', $handler);
            $config[] =         '        public: true';
            $config[] =         '        calls:';
            $config[] =         '            - [ setContainer, [ \'@service_container\' ] ]';
            $config[] =         '        tags:';

            switch (substr($message, -1)) {
                case 'd': // Command
                    $config[] = sprintf('            - { name: tactician.handler, command: %s }', $message);
                    break;

                case 'y': // Query
                    $config[] = sprintf('            - { name: tactician.handler, command: %s, bus: query }', $message);
                    break;

                default:
                    throw new LogicException(sprintf('Class name of handler "%s" is not valid.', $handler));
            }
            $config[] = '';
        }

        $filesystem = new Filesystem();
        $filesystem->dumpFile($dir.sprintf('%1$sapp%1$sconfig%1$sbus-services.yml', DIRECTORY_SEPARATOR), implode("\n", $config));

        $output->writeln('<info>Config bus-services.yml is created.</info>');
    }
}
