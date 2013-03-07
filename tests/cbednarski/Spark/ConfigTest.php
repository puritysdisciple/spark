<?php

require_once(__DIR__ . '/../../../vendor/autoload.php');

use cbednarski\Spark\Config;
use Symfony\Component\Yaml\Yaml;

class ConfigTest extends PHPUnit_Framework_TestCase
{
    private $config;
    private $standard_config;

    public function setup()
    {
        # __DIR__ can't be used in the variable definition so we need to use setup()
        $this->config = new Config(__DIR__);
        $this->standard_config = __DIR__ . '/../../assets/spark.yml';
    }

    public function testStandardConfig()
    {
        $config = Config::loadFile($this->standard_config);
        $this->assertEquals(Yaml::parse($this->standard_config), $config->getData());
    }

    public function testPartialConfig()
    {
        $config_file = __DIR__ . '/../../assets/spark_incomplete.yml';
        $config = Config::loadFile($config_file);
        $this->assertEquals($config->getData(), Yaml::parse($this->standard_config));

        # These configs should be filled in even though they aren't in the .yml
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
            'plugins' => 'src/plugins/',
            'target' => 'my/custom/build/target/',
            'cache' => 'build/cache/', # This one isn't specified in .yml though
            'localization' => array(
                'path' => 'po_files/',
                'localize' => array(
                    'en_US' => 'en',
                    'fr_FR' => 'fr',
                    'de_DE' => 'de',
                )
            )
        );

        $this->assertEquals($expected, $config->getData());
    }

    public function testBasePath()
    {
        $base_path = 'monkey';
        $this->config->setBasePath($base_path);
        $this->assertEquals($base_path, $this->config->getBasePath());
    }

    public function testBasePathFromConfig()
    {
        $base_path = realpath(__DIR__ . '/../../assets/');
        $config_file = $base_path . '/spark_custom.yml';
        $config = Config::loadFile($config_file);

        $this->assertEquals($base_path, $config->getBasePath());
    }

    public function testGetFullPath()
    {
        $this->assertEquals(
            $this->config->getBasePath() . DIRECTORY_SEPARATOR . 'target',
            $this->config->getFullPath($this->config->target)
        );

        $this->assertEquals(
            $this->config->getBasePath() . DIRECTORY_SEPARATOR . 'locale',
            $this->config->getFullPath($this->config->locale)
        );
    }

    /**
     * @expectedException RuntimeException
     */
    public function testLoadError()
    {
        $path = __DIR__ . '/asdfasoiuerew';
        $config = Config::loadFile($path . '/spark.yml');
    }

    public function testMagicPropertyAccess()
    {
        $test_val = 'pie';

        $this->config->test1 = $test_val;
        $this->assertEquals($test_val, $this->config->test1, 'Test basic __get / __set');

        $this->assertEquals(__DIR__, $this->config->getBasePath());
        $this->config->base_path = 'somerandompath';
        $this->assertEquals(__DIR__, $this->config->getBasePath(), 'Base path should not change with property access');

        $this->assertNull($this->config->blah, 'Blah doesn\'t exist yet.');
    }

    public function testGetTargetPath()
    {
        $this->assertEquals(__DIR__ . '/target', $this->config->getTargetPath());
    }

    public function testGetLocalePath()
    {
        $this->assertEquals(__DIR__ . '/locale', $this->config->getLocalePath());
    }

    public function testGetPagePath()
    {
        $this->assertEquals(__DIR__ . '/pages', $this->config->getPagePath());
    }

    public function testGetAssetPath()
    {
        $this->assertEquals(__DIR__ . '/assets', $this->config->getAssetPath());
    }

    public function testGetPluginPath()
    {
        $this->assertEquals(__DIR__ . '/plugins', $this->config->getPluginPath());
    }

    public function testGetLocaleFormat()
    {
        $this->assertEquals('po', $this->config->getLocaleFormat());
    }

}
