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
        $compiler->buildPages();

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
        $compiler->buildPages();

        $this->assertFalse(file_exists($config->getTargetPath() . '/index.html'), 'We should not have an index.html now');

        FileUtils::recursiveDelete($path);
    }
}
