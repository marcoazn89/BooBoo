<?php
namespace HTTP\request;
require_once('RequestHeader.php');

use HTTP\request\RequestHeader;

class AcceptType extends RequestHeader {
	public static function getContent() {
		if(count(static::$content) == 0) {
			return self::contentPriority($_SERVER['HTTP_ACCEPT']);
		}
		else {
			return static::$content;
		}
	}

	public static function getDefault() {
		return array('application/xml','text/html','application/xhtml+xml','application/json','text/plain');
	}
}
