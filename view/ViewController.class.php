<?php

class ViewController {
	public function render() {
		ob_start();
		include dirname(__FILE__) . '/shared/header.php';
		render_view();
		include dirname(__FILE__) . '/shared/footer.php';
		ob_end_flush();
	}
}