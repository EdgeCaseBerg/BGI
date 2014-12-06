<?php

class ViewController {
	public function render($viewName) {
		logMessage("Rendering View $viewName", LOG_LVL_VERBOSE);
		ob_start();
		include dirname(__FILE__) . '/shared/header.php';
		include dirname(__FILE__) . '/' . $viewName . '.php';
		include dirname(__FILE__) . '/shared/footer.php';
		ob_end_flush();
	}
}