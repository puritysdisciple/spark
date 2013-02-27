<?php

namespace cbednarski\Spark\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Watch extends Command
{
    protected function configure()
    {
        $this->setName('watch');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<error>Not yet implemented.</error>');
    }

}