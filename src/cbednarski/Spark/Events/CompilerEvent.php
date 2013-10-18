<?php
namespace cbednarski\Spark\Events;

use Symfony\Component\EventDispatcher\Event;

class CompilerEvent extends Event {
	protected $compiler;

	public function __construct ($compiler) {
		$this->compiler = $compiler;
	}

	public function getCompiler () {
		return $this->compiler;
	}
}
