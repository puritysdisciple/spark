<?php

require_once(__DIR__ . '/../../../vendor/autoload.php');

class SparkCompilerTest extends PHPUnit_Framework_TestCase
{
	public function __construct($source, $target, $cache = null)
	{

	}

	public function compile()
	{
		$source = $this->validatePath($input->getArgument('source'));
		$target = $this->validatePath($input->getArgument('target'));

		$loader = new Twig_Loader_Filesystem($source);

		$twig = new Twig_Environment($loader, array(
			'cache' => '',
		));

		$render = $twig->render('index.html', array('name' => 'Fabien'));
	}
}