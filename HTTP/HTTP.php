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
			Status::getInstance()->set($code, $init);
		}
		else {
			$status = Status::getInstance();
			$status->set($code, $init);
			self::$headers['status'] = $status;
		}
	}

	public static function contentType($contentType, $init = false) {
		if($init) {
			ContentType::getInstance()->set($contentType, $init);
		}
		else {
			$content = ContentType::getInstance();
			$content->set($contentType, $init);
			self::$headers['type'] = $content;
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
			Language::getInstance()->set($language, $init);
		}
		else {
			$lang = Language::getInstance();
			$lang->set($language, $init);
			self::$headers['language'] = $lang;
		}
	}

	public static function authenticate($auth = array(), $init = false) {
		if($init) {
			WWWAuthenticate::getInstance()->set($auth, $init);
		}
		else {
			$authenticate = WWWAuthenticate::getInstance();
			$authenticate->set($auth, $init);
			self::$headers['authenticate'] = $authenticate;
		}
	}

	public static function cache($cache = null, $init = false) {
		if($init) {
			CacheControl::getInstance()->set($cache, $init);
		}
		else {
			$cache = CacheControl::getInstance();
			$cache->set($cache, $init);
			self::$headers['cache'] = $cache;
		}
	}

	public static function body($content) {
		self::$body = $content;
	}

	public static function sendResponse() {
		foreach(self::$headers as $k => $header) {
			$header->sendHeader();
		}

		if( ! empty(self::$body)) {
			echo self::$body;
		}
	}
}
