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

        $index_path = $path . '/' . $config->target . '/index.html';

        # Check that stuff was built
        $this->assertTrue(file_exists($index_path));
        $this->assertTrue(file_exists($path . '/' . $config->target . '/assets/css/main.css'));

        # Check that the compiled version looks the way it's supposed to
        $this->assertTrue(strpos(file_get_contents($index_path), 'href="/assets/css/main.css') !== false);

        # Check that sample plugin worked
        $this->assertEquals($compiler->testParam, "test!");

        FileUtils::recursiveDelete($path);
    }
}
