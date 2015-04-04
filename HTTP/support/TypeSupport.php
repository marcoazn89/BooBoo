<?php
namespace HTTP\support;
require_once('ContentSupport.php');
use HTTP\support\ContentSupport;

class TypeSupport extends ContentSupport {

	public static function getDefault() {
		return array('text/html','application/xhtml+xml','application/xml');
	}
}
