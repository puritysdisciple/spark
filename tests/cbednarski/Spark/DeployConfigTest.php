<?php

require_once(__DIR__ . '/../../../vendor/autoload.php');

use cbednarski\Spark\Config;
use cbednarski\Spark\DeployConfig;

class DeployConfigTest extends \PHPUnit_Framework_TestCase
{
    /** @var Config */
    private $config;

    public function setUp()
    {
        $this->config = Config::loadFile(__DIR__ . '/../../assets/spark.yml');
    }

    /**
     * @expectedException RuntimeException
     */
    public function testException()
    {
        new DeployConfig($this->config, 'blah');
    }

    public function testGetEnvironments()
    {
        $deploy = new DeployConfig($this->config);

        $environments = array(
            'prod',
            'ci'
        );

        $this->assertEquals($environments, $deploy->getDeployments());
    }

    public function testGetEnvByName()
    {
        $deploy = new DeployConfig($this->config);

        $prod = array(
            'aws' => array(
                'key' => 'AAAAAAAAAAAAAAAAAAAA',
                'secret' => 'eeeeeeeeeeeeeeee/FFFFFFFFFFFFFFFFFFFFFFF',
                'bucket' => 'mysite.com'
            )
        );

        $this->assertEquals($prod, $deploy->getDeployByName('prod'));

        $this->assertFalse($deploy->getDeployByName('dev'));
    }
}
