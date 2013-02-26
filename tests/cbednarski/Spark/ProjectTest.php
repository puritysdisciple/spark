<?php

require_once(__DIR__ . '/../../../vendor/autoload.php');

use cbednarski\Spark\Project;
use cbednarski\Spark\FileUtils;

class ProjectTest extends PHPUnit_Framework_TestCase
{
	public function testInit()
	{
		$path = realpath(__DIR__ . '/../../') . '/test_project';

		Project::init($path);

		$this->assertTrue(file_exists($path . '/.gitignore'));
		$this->assertTrue(file_exists($path . '/spark.yml'));
		$this->assertTrue(file_exists($path . '/src/assets'));
		$this->assertTrue(file_exists($path . '/src/layouts'));
		$this->assertTrue(file_exists($path . '/src/pages'));
		$this->assertTrue(file_exists($path . '/build/target'));
		$this->assertTrue(file_exists($path . '/build/cache'));
		$this->assertTrue(file_exists($path . '/locale/en_US/LC_MESSAGES'));

		FileUtils::recursiveDelete($path);
	}
}