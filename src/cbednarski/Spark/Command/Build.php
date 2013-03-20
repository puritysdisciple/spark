<?php

namespace cbednarski\Spark\Command;

use cbednarski\Spark\Config;
use cbednarski\Spark\Compiler;
use cbednarski\Spark\FileUtils;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Build extends Command
{
    protected function configure()
    {
        $this->setName('build');
        $this->setDescription('Build a spark project under the specified directory');
        $this->addArgument(
            'directory',
            InputArgument::OPTIONAL,
            'spark project folder (defaults to the current directory if not specified)'
        );
        $this->addOption(
            'no-clean',
            null,
            InputOption::VALUE_NONE,
            'If set, the build command will not clean the target folder before building'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $directory = $input->getArgument('directory');

        if (!$directory) {
            $directory = getcwd();
        }

        $config = Config::loadFile($directory . '/spark.yml');
        $compiler = new Compiler($config);
        $compiler->setOutput($output);

        # Clean out the build target unless no-clean is detected
        if (!$input->getOption('no-clean')) {
            FileUtils::recursiveDelete($config->getFullPath('target'));
        }

        $output->writeln('<info>Building spark under ' . realpath($directory) . '</info>');
        $compiler->buildPages();
        $output->writeln('<info>Build complete.</info>');
    }
}
