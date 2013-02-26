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
        
    }
}