<?php

namespace cbednarski\Spark\Command;

use cbednarski\Spark\Aws\AwsConfig;
use cbednarski\Spark\Aws\AwsDeploy;
use cbednarski\Spark\Config;
use cbednarski\Spark\FileUtils;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Deploy extends Command
{
    protected function configure()
    {
        $this->setName('deploy');
        $this->setDescription('Deploy a spark site from the target folder');
        $this->addArgument(
            'name',
            InputArgument::OPTIONAL,
            'which deploy configuration to use'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');

        $directory = getcwd();
        $config = Config::loadFile($directory . '/spark.yml');

        $help_text = <<<HEREDOC
The correct deployment configuration format is (for example):

prod-deployment:
  aws:
    key: your-access-key-id
    secret: your-secret-access-key
    bucket: your-s3-bucket-name
other-deployment:
  rsync:
    target: user@example.com:/some/path
HEREDOC;

        try {
            $aws = $config->getAwsConfig();
        } catch(\RuntimeException $e) {
            throw new \RuntimeException(
                'No deployment configuration found. You must create one at'
                . PHP_EOL . $config->getBasePath() . DIRECTORY_SEPARATOR . 'spark-deploy.yml'
                . PHP_EOL . PHP_EOL . $help_text
            );
        }

        if(!$aws->loadNamedConfig($name)) {
            throw new \RuntimeException(
                'Unable to load the specified deployment: ' . $name
                . PHP_EOL . 'Verify this deployment is configured in '
                . PHP_EOL . $config->getBasePath() . DIRECTORY_SEPARATOR . 'spark-deploy.yml'
                . PHP_EOL . PHP_EOL . $help_text
            );
        }

        $deploy = new AwsDeploy($config);

        $output->writeln('<info>Deploying spark site from ' . realpath($config->getTargetPath()) . ' using deploy ' . $name . '</info>');

        $deploy->upload($config->getTargetPath(), $aws->getBucket());
        $output->writeln('<info>Deploy complete</info>');
    }
}
