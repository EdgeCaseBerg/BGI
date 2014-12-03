<?php
/* An Entity simply must have an id. That is all */
class Entity extends StdClass {
	public $id;
	public function getId() {
		return $this->id;
	}
	public function setId($id) {
		$this->id = $id;
	}
}

?>