<?php

require_once(__DIR__ . '/../../../vendor/autoload.php');

use cbednarski\Spark\Compiler;
use cbednarski\Spark\Config;
use cbednarski\Spark\Project;
use cbednarski\Spark\FileUtils;

class CompilerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @group compiler
     */
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

    /**
     * @group compiler
     */
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

    public function testBuildLocales()
    {
        # Setup project
        $path = realpath(__DIR__ . '/../../') . '/test_buildlocales';
        Project::init($path);
        $config = Config::loadFile($path . '/spark.yml');

        $temp = $config->localization;
        $temp['localize'] = array(
            'en_US' => 'en',
            'fr_FR' => 'fr'
        );
        $config->localization = $temp;

        $compiler = new Compiler($config);
        $compiler->buildLocales();

        $this->assertTrue(file_exists($path . '/target/en/index.html'));
        $en = strpos(file_get_contents($path . '/target/en/index.html'), 'Welcome to Spark! This demo is here to help you get up and running quickly.');
        $this->assertFalse($en === false);

        $this->assertTrue(file_exists($path . '/target/fr/index.html'));
        $fr = strpos(file_get_contents($path . '/target/fr/index.html'), 'Bienvenue sur Spark! Cette démo est là pour vous aider à démarrer rapidement.');
        $this->assertFalse($fr === false);

        $this->assertTrue(file_exists($path . '/target/fr/index.html'));
        $fr = strpos(file_get_contents($path . '/target/fr/index.html'), 'Standard: fr_FR.');
        $this->assertFalse($fr === false);

        $this->assertTrue(file_exists($path . '/target/fr/index.html'));
        $fr = strpos(file_get_contents($path . '/target/fr/index.html'), 'Url-Code: fr.');
        $this->assertFalse($fr === false);

        FileUtils::recursiveDelete($path);
    }

    public function testIgnore()
    {
        # Setup project
        $path = realpath(__DIR__ . '/../../') . '/test_ignore';
        Project::init($path);
        $config = Config::loadFile($path . '/spark.yml');

        $ignores = array('index');
        $config->ignore = $ignores;
        $this->assertEquals($ignores, $config->getIgnoredPaths(), 'Verify ignore config stuck');

        $compiler = new Compiler($config);
        $compiler->build();

        $this->assertFalse(file_exists($config->getTargetPath() . '/index.html'), 'We should not have an index.html now');

        FileUtils::recursiveDelete($path);
    }
}
