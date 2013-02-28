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

        $target_file = $path . '/index.html';

        $compiler = new Compiler($config);
        $compiler->compile('index.html.twig', $target_file);

        $generated = file_get_contents($target_file);
        $expected = file_get_contents(__DIR__ . '/../../assets/sample_render.html');

        $this->assertEquals($expected, $generated);
        unlink($target_file);
        FileUtils::recursiveDelete($path);
    }

    public function testBuild()
    {
        # Setup project
        $path = realpath(__DIR__ . '/../../') . '/test_compile';
        Project::init($path);
        $config = Config::loadFile($path . '/spark.yml');

        # Initialize the compiler
        $compiler = new Compiler($config);

        # Build all the things
        $compiler->build();

        # Check that stuff was built
        $this->assertTrue(file_exists($path . '/' . $config->target . '/index.html'));
        FileUtils::recursiveDelete($path);
    }
}
