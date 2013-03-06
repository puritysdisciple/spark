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

        Project::init($path);

        $config = Config::loadFile($path . '/spark.yml');

        $this->assertTrue(file_exists($path . '/.gitignore'));
        $this->assertTrue(file_exists($path . '/spark.yml'));
        $this->assertTrue(file_exists($path . '/'. $config->assets));
        $this->assertTrue(file_exists($path . '/'. $config->assets . '/css/main.css'));
        $this->assertTrue(file_exists($path . '/'. $config->layouts));
        $this->assertTrue(file_exists($path . '/'. $config->layouts . '/layout.html.twig'));
        $this->assertTrue(file_exists($path . '/'. $config->pages));
        $this->assertTrue(file_exists($path . '/'. $config->pages . '/index.html.twig'));
        $this->assertTrue(file_exists($path . '/'. $config->target));
        $this->assertTrue(file_exists($path . '/'. $config->locale . '/en_US/LC_MESSAGES'));
        $this->assertTrue(file_exists($path . '/'. $config->plugins . '/sample_plugin.php'));

        FileUtils::recursiveDelete($path);
    }
}
