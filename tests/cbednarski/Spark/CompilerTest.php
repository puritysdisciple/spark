<?php

require_once(__DIR__ . '/../../../vendor/autoload.php');

use cbednarski\Spark\Compiler;

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

    public function testBuild()
    {

    }

    public function testCompile()
    {
        $compiler = new Compiler(
            realpath(__DIR__ . '/../../../src/cbednarski/Spark/Resources/'),
            __DIR__ . '/../test_compile/'
        );

        $compiler->compile('index.html.twig', __DIR__ . '/index.html');

        $generated = file_get_contents(__DIR__ . '/index.html');
        $expected = file_get_contents(__DIR__ . '/../../assets/sample_render.html');

        $this->assertEquals($expected, $generated);
        unlink(__DIR__ . '/index.html');
    }
}