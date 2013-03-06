<?php

require_once(__DIR__ . '/../../../vendor/autoload.php');

use cbednarski\Spark\Config;
use cbednarski\Spark\Project;
use cbednarski\Spark\FileUtils;

class ProjectTest extends PHPUnit_Framework_TestCase
{
    public function testInit()
    {
        $path = realpath(__DIR__ . '/../../') . '/test_project';

        $config = Project::init($path);

        $this->assertEquals($config, Config::loadFile($path . '/spark.yml'));

        $this->assertTrue(file_exists($path . '/.gitignore'));
        $this->assertTrue(file_exists($path . '/spark.yml'));
        $this->assertTrue(file_exists($path . '/' . $config->assets));
        $this->assertTrue(file_exists($path . '/' . $config->assets . '/css/main.css'));
        $this->assertTrue(file_exists($path . '/' . $config->layouts));
        $this->assertTrue(file_exists($path . '/' . $config->layouts . '/layout.html.twig'));
        $this->assertTrue(file_exists($path . '/' . $config->pages));
        $this->assertTrue(file_exists($path . '/' . $config->pages . '/index.html.twig'));
        $this->assertTrue(file_exists($path . '/' . $config->target));
        $this->assertTrue(file_exists($path . '/' . $config->locale . '/en_US/LC_MESSAGES'));
        $this->assertTrue(file_exists($path . '/' . $config->locale . '/en_US/LC_MESSAGES/messages.po'));
        $this->assertTrue(file_exists($path . '/' . $config->locale . '/fr_FR/LC_MESSAGES'));
        $this->assertTrue(file_exists($path . '/' . $config->locale . '/fr_FR/LC_MESSAGES/messages.po'));
        $this->assertTrue(file_exists($path . '/' . $config->plugins . '/sample_plugin.php'));

        FileUtils::recursiveDelete($path);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInitException()
    {
        Project::init(null);
    }

    public function testGetAvailableLocales()
    {
        $path = realpath(__DIR__ . '/../../') . '/test_avail_locales';

        $config = Project::init($path);

        $locales = Project::getAvailableLocales($config);
        $this->assertEquals(array('en_US', 'fr_FR'), $locales);

        FileUtils::recursiveDelete($path);
    }

    public function testGetActiveLocales()
    {
        $path = realpath(__DIR__ . '/../../') . '/test_active_locales';

        $config = Project::init($path);
        $config->localize = array('fr_FR' => 'fr');

        $locales = Project::getActiveLocales($config);
        $this->assertEquals(array('fr_FR'), $locales);

        FileUtils::recursiveDelete($path);
    }
}
