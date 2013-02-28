<?php

require_once(__DIR__ . '/../../../vendor/autoload.php');

use cbednarski\Spark\Config;
use Symfony\Component\Yaml\Yaml;

class ConfigTest extends PHPUnit_Framework_TestCase
{
    private $standard_config;

    public function setup()
    {
        # __DIR__ can't be used in the variable definition so we need to use setup()
        $this->standard_config = __DIR__ . '/../../assets/spark.yml';
    }

    public function testStandardConfig()
    {
        $config = Config::loadFile($this->standard_config);
        $this->assertEquals($config->getData(), Yaml::parse($this->standard_config));
    }

    public function testPartialConfig()
    {
        $config_file = __DIR__ . '/../../assets/spark_incomplete.yml';
        $config = Config::loadFile($config_file);
        $this->assertEquals($config->getData(), Yaml::parse($this->standard_config));

        # These configs should be filled in even though they aren't in the .yml
        $this->assertEquals('build/cache/', $config->cache);
        $this->assertEquals('locale/', $config->locale);
    }

    public function testCustomConfig()
    {
        $base_path = realpath(__DIR__ . '/../../assets');
        $config_file = $base_path . '/spark_custom.yml';
        $config = Config::loadFile($config_file);

        # These configs are overridden completely
        $expected = array(
            'pages' => 'src/pages/',
            'assets' => 'src/assets/',
            'layouts' => 'src/layouts/',
            'target' => 'my/custom/build/target/',
            'cache' => 'build/cache/', # This one isn't specified in .yml though
            'locale' => 'po_files/',
            'localize' => array(
                'en_US' => 'en',
                'fr_FR' => 'fr',
                'de_DE' => 'de',
            )
        );

        $this->assertEquals($expected, $config->getData());
    }

    public function testGetBaseDir()
    {
        $base_path = realpath(__DIR__ . '/../../assets/');
        $config_file = $base_path . '/spark_custom.yml';
        $config = Config::loadFile($config_file);

        $this->assertEquals($base_path, $config->getBasePath());
    }
}
