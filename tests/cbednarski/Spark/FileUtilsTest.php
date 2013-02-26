<?php

require_once(__DIR__ . '/../../../vendor/autoload.php');

use cbednarski\Spark\FileUtils;

class FileUtilsTest extends PHPUnit_Framework_TestCase
{
    public funciton mkdirIfNotExists()
    {
        $path = __DIR__.'/magicpath';
        $this->assertFalse(file_exists($path));
        FileUtils::mkdirIfNotExists($path);
        $this->assertTrue(file_exists($path));
        $this->assertTrue(is_dir($path));
        rmdir($path);
        $this->assertFalse(file_exists($path))
    }

    public function testDirIsEmpty()
    {
        $this->assertFalse(FileUtils::dirIsEmpty(__DIR__));
    }
}