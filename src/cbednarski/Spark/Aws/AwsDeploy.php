<?php

namespace cbednarski\Spark\Aws;

use Aws\S3\S3Client;
use cbednarski\Spark\Config;

# @see http://blogs.aws.amazon.com/php/post/Tx2W9JAA7RXVOXA
class AwsDeploy
{
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function upload($source, $target)
    {
        $aws = $this->config->getAwsConfig(); # Save some typing

        $client = S3Client::factory(array(
            'key' => $aws->getKey(),
            'secret' => $aws->getSecret()
        ));

        $options = array();

        $client->uploadDirectory($this->config->getTargetPath(), $aws->getBucket(), null, $options);
    }
}