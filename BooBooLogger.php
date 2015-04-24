<?php
namespace BooBoo;

require_once('BooBooLog.php');

use BooBoo\BooBooLog;

class BooBooLogger implements BooBooLog {

	//Singleton Pattern: Can't create an instance
	protected function __construct() {}
	//Singleton Pattern: Can't clone
	protected function __clone() {}
	//Singleton Pattern: Can't deserialize
	protected function __wakeup() {}

	final public static function getInstance() {
		static $instance = null;

		if(is_null($instance)) {
			$instance = new static();
		}

		return $instance;
	}

	public function log($message) {
		error_log($message);
	}
}
