<?php

namespace cbednarski\Spark\Command;

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

        if (is_dir(realpath($directory))) {
            if (count(scandir($directory)) > 2) {
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
        }

        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $path = realpath($directory) . '/';

        // Create folders
        foreach (array('source', 'target', 'cache', 'locale/en_US/LC_MESSAGES') as $folder) {
            mkdir($path . $folder, 0755, true);
        }

        // Add .gitignore file
        $gitignore = <<<HEREDOC
build/
vendor/
HEREDOC;
        file_put_contents($path . '.gitignore', $gitignore);

        // Add spark.yml
        $spark = <<<HEREDOC
source: src/
target: build/target/
cache:  build/cache/
locale: locale/
localize: all
HEREDOC;
        file_put_contents($path . 'spark.yml', $spark);


    }

}