<?php
namespace HTTP\support;
abstract class ContentSupport {
	protected static $content = array();

	abstract public static function getDefault();
	
	public static function addSupport(Array $content) {
		static::$content = $content;
	}

	public static function getSupport() {
		if(count(static::$content) == 0) {
			return static::getDefault();
		}
		else {
			return static::$content;
		}		
	}
}
