<?php
namespace HTTP;

use HTTP\response\Status;
use HTTP\response\ContentType;
use HTTP\response\Language;
use HTTP\response\WWWAuthenticate;
use HTTP\response\CacheControl;
use HTTP\request\AcceptType;
use HTTP\support\TypeSupport;
use HTTP\support\ContentSupport;
use HTTP\support\LanguageSupport;
use HTTP\ContentNegotiation;

class HTTP {
	private static $headers = array();
	private static $body = '';

	public static function status($code, $init = false) {
		if($init) {
			return new Status($code, $init);
		}
		else {
			array_push(self::$headers, new Status($code, $init));
		}
	}

	public static function contentType($contentType, $init = false) {
		if($init) {
			return new ContentType($contentType, $init);
		}
		else {
			array_push(self::$headers, new ContentType($contentType, $init));
		}
	}

	public static function negotiateContentType($strongNegotiation = false) {
		$negotiation = ContentNegotiation::negotiate(AcceptType::getContent(), TypeSupport::getSupport());

		$content = '';

		if(count($negotiation) > 0) {
			$content = $negotiation[0];
		}
		else {
			if($strongNegotiation) {
				self::fail();
			}
			else {
				$content = TypeSupport::getSupport();
				$content = $content[0];
			}
		}

		return $content;
	}

	public static function fail($statusCode) {
		$supported = TypeSupport::getSupport();
		$clientSupport = AcceptType::getContent();

		$supported = implode(',', $supported);
		$clientSupport = implode(',',$clientSupport);

		self::status(406);
		self::contentType(ContentType::TEXT);
		self::body("NOT SUPPORTED\nThis server does not support {$supported}.\nSupported formats: {$clientSupport}");
		self::sendResponse();
	}

	public static function language($language, $init = false) {
		if($init) {
			return new Language($language, $init);
		}
		else {
			array_push(self::$headers, new Language($language, $init));
		}
	}

	public static function authenticate($type = 'basic', Array $other = null, $init = false) {
		if($init) {
			return new WWWAuthenticate($type, $init, $other);
		}
		else {
			array_push(self::$headers, new WWWAuthenticate($type, $other, $init));
		}
	}

	public static function cache(CacheControl $cache, $init = false) {
		if($init) {
			return $cache->sendHeader();
		}
		else {
			array_push(self::$headers, $cache);
		}
	}

	public static function body($content) {
		self::$body = $content;
	}

	public static function sendResponse() {
		if( ! empty(self::$body)) {
			echo self::$body;
		}

		foreach(self::$headers as $header) {
			$header->sendHeader();
		}
	}
}
