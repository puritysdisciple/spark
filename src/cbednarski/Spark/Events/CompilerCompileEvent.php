<?php
namespace cbednarski\Spark\Events;

use cbednarski\Spark\Events\CompilerEvent;

class CompilerCompileEvent extends CompilerEvent {
	protected $params = array();

	public function addParams ($params) {
		$this->params = array_merge($this->params, $params);
	}

	public function getParams () {
		return $this->params;
	}
}
