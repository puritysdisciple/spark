<?php

namespace cbednarski\Spark\Command;

require_once(__DIR__ . '/../../../../vendor/autoload.php');

use cbednarski\Spark\FileUtils;
use cbednarski\Spark\Project;

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
        $this->setDescription('Initialize a new spark project');
        $this->addArgument('directory', InputArgument::OPTIONAL, 'target directory (will be created if it doesn\'t exist)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $directory = $input->getArgument('directory');

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

        Project::init($directory);
        $output->writeln('<info>Spark project initialized under ' . realpath($directory) . '</info>');
    }

}