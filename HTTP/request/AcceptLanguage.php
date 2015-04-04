<?php
namespace HTTP\request;
require_once('RequestHeader.php');

use HTTP\request\RequestHeader;

class AcceptLanguage extends RequestHeader {
	public static function getContent() {
		if(count(static::$content) == 0) {
			return self::contentPriority($_SERVER['HTTP_ACCEPT_LANGUAGE']);
		}
		else {
			return static::$content;
		}
	}

	public static function getDefault() {
		return array('en-US','en','es');
	}
}
