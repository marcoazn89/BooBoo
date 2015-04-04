<?php
namespace HTTP\response;

require_once('HttpHeader.php');

use HTTP\response\HTTPHeader;

class WWWAuthenticate implements HTTPHeader {

	public $authenticate;
	
	public function __construct($type = 'basic', Array $other = null, $send = false) {
		$this->authenticate = $type;

		if( ! is_null($other)) {
			$temp = array();
			
			foreach($other as $key => $val) {
				array_push($temp, "{$key}={$val}");
			}

			$this->authenticate .= ' '.implode(', ', $temp);
		}

		if($send) {
			$this->sendHeader();
		}
	}

	public function sendHeader() {
		return header("WWW-Authenticate: {$this->authenticate}");
	}
}
