<?php

require_once(__DIR__ . '/../../../vendor/autoload.php');

use cbednarski\Spark\Config;
use cbednarski\Spark\Aws\AwsConfig;

class AwsConfigTest extends \PHPUnit_Framework_TestCase
{
    /** @var Config */
    private $config;

    public function setUp()
    {
        $this->config = Config::loadFile(__DIR__ . '/../../assets/spark.yml');
    }

    public function testGetConfigs()
    {
        $aws = new AwsConfig($this->config);

        $this->assertNull($aws->getKey());
        $this->assertNull($aws->getSecret());
        $this->assertNull($aws->getBucket());

        $aws->setKey('key');
        $aws->setSecret('secret');
        $aws->setBucket('bucket');

        $this->assertEquals('key', $aws->getKey());
        $this->assertEquals('secret', $aws->getSecret());
        $this->assertEquals('bucket', $aws->getBucket());
    }

    public function testLoadNamedConfig()
    {
        $aws = new AwsConfig($this->config);
        $aws->loadNamedConfig('prod');

        $this->assertFalse($aws === false, 'Verify that the config was loaded from a file');

        $this->assertEquals('AAAAAAAAAAAAAAAAAAAA', $aws->getKey());
        $this->assertEquals('eeeeeeeeeeeeeeee/FFFFFFFFFFFFFFFFFFFFFFF', $aws->getSecret());
        $this->assertEquals('mysite.com', $aws->getBucket());
    }

    public function testLoadFromEnvironment()
    {
        putenv('SPARK_AWS_KEY=keyx');
        putenv('SPARK_AWS_SECRET=secretx');
        putenv('SPARK_AWS_BUCKET=bucketx');

        $aws = new AwsConfig($this->config);
        $aws->loadFromEnvVars();

        $this->assertEquals('keyx', $aws->getKey());
        $this->assertEquals('secretx', $aws->getSecret());
        $this->assertEquals('bucketx', $aws->getBucket());
    }
}
