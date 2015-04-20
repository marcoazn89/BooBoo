<?php
namespace HTTP\response;

require_once('HttpHeader.php');

use HTTP\response\HTTPHeader;

class WWWAuthenticate extends HTTPHeader {

	public $auth = array();
	public $type = 'Basic';
	
	public function set($auth = array(), $send = false) {
		$this->auth = $auth;

		$this->headerString = "WWW-Authenticate: {$this->type} ";

		foreach($this->auth as $key => $val) {
			$this->headerString .= "{$key}={$val},";
		}

		$this->headerString = rtrim($this->headerString, ",");

		if($send) {
			$this->sendHeader();
		}
	}

	/**
	 * Set the authentication type.
	 * Default is Basic
	 * @param String $type
	 */
	public function setType($type) {
		$this->type = $type;
	}
}
