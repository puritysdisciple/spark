<?php

namespace cbednarski\Spark\Command;

require_once(__DIR__ . '/../../../../vendor/autoload.php');

use cbednarski\Spark\Config;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Preview extends Command
{
    protected function configure()
    {
        $this->setName('preview');
        $this->setDescription('Run php\'s built-in web server on the target directory');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $directory = getcwd();
        $config = Config::loadFile($directory . '/spark.yml');
        $output->writeln('<info>Starting built-in webserver on localhost:8000</info>');
        $output->writeln('<info>View your site homepage via http://localhost:8000/en_US/</info>');
        $output->writeln('<info>Press Ctrl-C to quit</info>');
        passthru("php -S 127.0.0.1:8000 -t target");
    }

}
