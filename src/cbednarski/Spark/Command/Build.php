<?php

namespace cbednarski\Spark\Command;

use cbednarski\Spark\Config;

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
        $this->addArgument('directory', InputArgument::OPTIONAL, 'Build a Spark project in the specified folder');
    }

	protected function execute(InputInterface $input, OutputInterface $output)
	{
        $directory = $input->getArgument('directory');

        $config = Config::loadFile($directory.'');
	}
}