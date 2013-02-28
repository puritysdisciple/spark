<?php

namespace cbednarski\Spark\Command;

use cbednarski\Spark\Config;
use cbednarski\Spark\FileUtils;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Clean extends Command
{
    protected function configure()
    {
        $this->setName('clean');
        $this->setDescription('Clean the build folder in this spark project');
        $this->addArgument(
            'directory',
            InputArgument::OPTIONAL,
            'spark project folder (defaults to the current directory if not specified)'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $directory = $input->getArgument('directory');

        if (!$directory) {
            $directory = getcwd();
        }

        $config = Config::loadFile($directory . '/spark.yml');

        $output->writeln('<info>Cleaning build folder under ' . realpath($directory) . '</info>');
        FileUtils::recursiveDelete($config->getFullPath('target'));
    }
}