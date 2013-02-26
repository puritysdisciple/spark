<?php

namespace cbednarski\Spark\Command;

require_once(__DIR__ . '/../../../../vendor/autoload.php');

use cbednarski\Spark\FileUtils;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Init extends Command
{
    protected function configure()
    {
        $this->setName('init');
        $this->addArgument('directory', InputArgument::OPTIONAL, 'Initialize a spark project in this directory');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $directory = $input->getArgument('directory');

        if (!$directory) {
            $directory = getcwd();
        }

        if (!FileUtils::dirIsEmpty($directory)) {
            $dialog = $this->getHelperSet()->get('dialog');
            // Prompt user if there are already files
            if (!$dialog->askConfirmation(
                $output,
                '<question>' . realpath(
                    $directory
                ) . ' already contains files. Do you want to continue? (y/N)</question>',
                false
            )
            ) {
                return;
            }
        }

        FileUtils::mkdirIfNotExists($directory);

        $path = realpath($directory) . '/';

        // Create folders
        FileUtils::mkdirs(array(
            'src/layouts',
            'src/pages',
            'src/assets',
            'build/target',
            'build/cache',
            'locale/en_US/LC_MESSAGES'
        ), $path);

        // Add .gitignore file
        FileUtils::concat($path . '.gitignore', __DIR__ . '/../Resources/gitignore');

        // Add spark.yml
        copy($path . 'spark.yml', __DIR__ . '/../Resources/spark.yml');
    }

}