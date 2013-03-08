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
        $this->assertTrue(file_exists($config->getLayoutPath()));
        $this->assertTrue(file_exists($config->getLayoutPath() . '/layout.html.twig'));
        $this->assertTrue(file_exists($config->getPagePath()));
        $this->assertTrue(file_exists($config->getPagePath() . '/index.html.twig'));
        $this->assertTrue(file_exists($config->getTargetPath()));
        $this->assertTrue(file_exists($config->getLocalePath() . '/en_US/LC_MESSAGES'));
        $this->assertTrue(file_exists($config->getLocalePath() . '/en_US/LC_MESSAGES/messages.po'));
        $this->assertTrue(file_exists($config->getLocalePath() . '/fr_FR/LC_MESSAGES'));
        $this->assertTrue(file_exists($config->getLocalePath() . '/fr_FR/LC_MESSAGES/messages.po'));
        $this->assertTrue(file_exists($config->getPluginPath() . '/sample_plugin.php'));

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
        $config->localization = array('localize' => array('fr_FR' => 'fr'));

        $locales = Project::getActiveLocales($config);
        $this->assertEquals(array('fr_FR'), $locales);

        FileUtils::recursiveDelete($path);
    }
}
