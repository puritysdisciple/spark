<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SparkCompile extends Command
{
	protected function configure()
	{
		$this->setName('twig:compile');
		$this->setDescription('Compile twig templates under the specified source directory and render them into html files in the target directory');
		$this->addArgument('source', InputArgument::REQUIRED, 'Path to Twig source files');
		$this->addArgument('target', InputArgument::REQUIRED, 'Render destination path');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{

	}

	private function validatePath($path)
	{
		$opath = getcwd();

		if($realpath = realpath($path)) {
			if(is_dir($realpath && $realpath != '/')) {
				$opath = $realpath;
			}
		}

		return $opath;
	}
}