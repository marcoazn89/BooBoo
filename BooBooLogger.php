<?php
namespace BooBoo;

require_once('BooBooLog.php');

use BooBoo\BooBooLog;

class BooBooLogger implements BooBooLog {

	public static $instance = null;
	
	public function __construct() {
		if( ! is_null(self::$instance)) {
			return self::$instance;
		}
		else {
			self::$instance = $this;
		}
	}

	public static function getInstance() {
		if(is_null(self::$instance)) {
			return new BooBooLogger();
		}
		else {
			return $instance;
		}
	}

	public function log($message) {
		error_log($message);
	}
}
