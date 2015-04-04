<?php
namespace HTTP\request;

abstract class RequestHeader {
	protected static $content = array();

	public static function setContent($overrideHeader = false, Array $content = null) {
		if($overrideHeader) {
			if(is_null($content)) {
				throw new Exception("Content must be set", 1);
			}

			static::$content = $content;
		}
		else {
			static::$content = static::getHeader();
		}
	}

	abstract public static function getContent();
	abstract public static function getDefault();

	protected static function contentPriority($string) {
		//sample: "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8" 
		if($string === '*/*') {
			return static::getDefault();
		}
		else {
			$content = array();
			$segments = explode(',', $string);

			foreach($segments as $segment) {
				$pieces = explode(';', $segment);

				if($pieces[0] === '*/*' && $pieces[1] === 'q=0.0') {
					continue;
				}

				array_push($content, $pieces[0]);
			}	
		}		

		return $content;
	}
}
