<?php

require_once(__DIR__ . '/../../../vendor/autoload.php');

use cbednarski\Spark\Compiler;
use cbednarski\Spark\Config;
use cbednarski\Spark\Project;
use cbednarski\Spark\FileUtils;


class CompilerTest extends PHPUnit_Framework_TestCase
{
    public function testGetTargetFilename()
    {
        $source = '/tmp/spark/source/';
        $target = '/tmp/spark/target/';

        $this->assertEquals('/tmp/spark/target/blah/testfile1.html',
            Compiler::getTargetFilename($source, $target, '/tmp/spark/source/blah/testfile1.html.twig'));

        $this->assertEquals('/tmp/spark/target/css/testfile1.css',
            Compiler::getTargetFilename($source, $target, '/tmp/spark/source/css/testfile1.css.twig'));

        $this->assertEquals('/tmp/spark/target/css/testfile1.css',
            Compiler::getTargetFilename($source, $target, '/tmp/spark/source/css/testfile1.css'));
    }

    public function testCompile()
    {
        $path = realpath(__DIR__ . '/../../test_compile/');
        Project::init($path);

        $config = Config::loadFile($path . '/spark.yml');
        $compiler = new Compiler($config);
        $compiler->compile('index.html.twig', __DIR__ . '/index.html');

        $generated = file_get_contents(__DIR__ . '/index.html');
        $expected = file_get_contents(__DIR__ . '/../../assets/sample_render.html');

        $this->assertEquals($expected, $generated);
        FileUtils::recursiveDelete($path);
    }

    public function testBuild()
    {
        // # Setup project
        // $project_path = realpath(__DIR__ . '/../..') . '/test_build';
        // Project::init($project_path);
        // $config = Config::loadFile($project_path . '/spark.yml');

        // # Initialize the compiler
        // $compiler = new Compiler($config);

        // # Build all the things
        // $compiler->build();

        // # Check that stuff was built
        // $this->assertEquals(file_exists($project_path . '/build/target/index.html'));
    }
}
