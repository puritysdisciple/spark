<?php

require_once(__DIR__ . '/../../../vendor/autoload.php');

use cbednarski\Spark\FileUtils;

class FileUtilsTest extends PHPUnit_Framework_TestCase
{
    public function mkdirIfNotExists()
    {
        $path = __DIR__ . '/magicpath';
        $this->assertFalse(file_exists($path));

        FileUtils::mkdirIfNotExists($path);
        $this->assertTrue(is_dir($path));

        rmdir($path);
        $this->assertFalse(file_exists($path));
    }

    public function testDirIsEmpty()
    {
        $this->assertFalse(FileUtils::dirIsEmpty(__DIR__));

        $path = __DIR__ . '/magicpath';
        FileUtils::mkdirIfNotExists($path);
        $this->assertTrue(FileUtils::dirIsEmpty($path));

        rmdir($path);
        $this->assertFalse(file_exists($path));
        $this->assertFalse(FileUtils::dirIsEmpty($path));
    }

    public function testExistsAndIsReadable()
    {
        $file = __DIR__ . '/magicfile';
        $this->assertFalse(FileUtils::existsAndIsReadable($file));

        touch($file);
        $this->assertTrue(FileUtils::existsAndIsReadable($file));

        chmod($file, 000);
        $this->assertFalse(FileUtils::existsAndIsReadable($file));

        chmod($file, 700);
        unlink($file);
        $this->assertFalse(file_exists($file));
    }

    public function testListFilesInDir()
    {
        $path = realpath(__DIR__ . '/../../assets/');
        $files = FileUtils::listFilesInDir($path);

        $expected = array(
            $path . '/spark.yml',
            $path . '/spark_custom.yml',
            $path . '/spark_incomplete.yml',
        );

        $this->assertEquals($expected, $files);
    }

    public function testMkdirs()
    {
        $path = __DIR__ . '/magicpath/';
        $dirs = array('pie', 'cake', 'icecream');

        FileUtils::mkdirs($dirs, $path);
        $this->assertTrue(is_dir($path));

        foreach($dirs as $dir) {
            $this->assertTrue(is_dir($path . $dir));
            rmdir($path . $dir);
            $this->assertFalse(is_dir($path . $dir));
        }

        rmdir($path);
        $this->assertFalse(is_dir($path));
    }

    public function testConcat()
    {
        file_put_contents('magicfile1', 'some stuff');
        FileUtils::concat('magicfile2', 'magicfile1');
        $this->assertEquals('some stuff', file_get_contents('magicfile2'));

        FileUtils::concat('magicfile1', 'magicfile2');
        $expected = 'some stuff' . PHP_EOL . 'some stuff';
        $this->assertEquals($expected, file_get_contents('magicfile1'));

        unlink('magicfile1');
        unlink('magicfile2');
    }
}