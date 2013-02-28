<?php

require_once(__DIR__ . '/../../../vendor/autoload.php');

use cbednarski\Spark\Compiler;
use cbednarski\Spark\Config;
use cbednarski\Spark\Project;
use cbednarski\Spark\FileUtils;

class CompilerTest extends PHPUnit_Framework_TestCase
{
    public function testCompile()
    {
        $path = realpath(__DIR__ . '/../../') . '/test_compile';
        Project::init($path);
        $config = Config::loadFile($path . '/spark.yml');

        $compiler = new Compiler($config);
        $compiler->compile('index.html.twig', __DIR__ . '/index.html');

        $generated = file_get_contents(__DIR__ . '/index.html');
        $expected = file_get_contents(__DIR__ . '/../../assets/sample_render.html');

        $this->assertEquals($expected, $generated);
        unlink(__DIR__ . '/index.html');
        FileUtils::recursiveDelete($path);
    }

    public function testBuild()
    {
        # Setup project
        $project_path = '/tmp/spark/test_build';
        Project::init($project_path);
        $config = Config::loadFile($project_path . '/spark.yml');

        # Initialize the compiler
        $compiler = new Compiler($config);

        # Build all the things
        $compiler->build();

        # Check that stuff was built
        $this->assertTrue(file_exists($project_path . '/build/target/index.html'));
        FileUtils::recursiveDelete($project_path);
    }
}
