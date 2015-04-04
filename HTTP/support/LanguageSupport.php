<?php
namespace HTTP\support;
require_once('ContentSupport.php');
use HTTP\support\ContentSupport;

class LanguageSupport extends ContentSupport {

	public static function getDefault() {
		return array('en-US','en','es');
	}
}
