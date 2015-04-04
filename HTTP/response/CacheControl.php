<?php
namespace HTTP\response;

require_once('HttpHeader.php');

use HTTP\response\HTTPHeader;
class CacheControl implements HTTPHeader {

	const CACHE_PUBLIC = 'public';
	const CACHE_PRIVATE = 'private';
	const NO_CACHE = 'no-cache';
	const NO_RESPONSE = 'no-response';
	const REVALIDATE = 'must-revalidate';
	const PROXY_REVALIDATE = 'proxy-revalidate';

	const MAX_AGE = 'max-age';
	const S_MAX_AGE = 's_maxage';

	const STALE_IF_ERROR = 'stale-if-error';
	const STALE_WHILE_REVALIDATE = 'stale-while-revalidate';

	public $cache = array(
		'expiration' => 'max-age=3600',
		'mode' => 'public',
		'channel' => null,
		'state' => null
		);

	public $cacheString = '';
	
	public function __construct($send = false) {
		if($send) {
			$this->sendHeader();
		}
	}

	public function setExpirationType($type = self::MAX_AGE, $duration = 3600) {
		$this->cache['expiration'] = "max-age={$duration}";
	}

	public function setMode($mode = self::CACHE_PUBLIC) {
		$this->cache['mode'] = $mode;
	}

	public function setChannel($url) {
		$this->cache['channel'] = "channel={$url}";
	}

	public function setStale($stale) {
		$this->cache['stale'] = $stale;
	}

	private function setCacheString() {
		$temp = array();

		foreach($this->cache as $extension => $string) {
			if( ! is_null($string)) {
				array_push($temp, $string);
			}
		}

		$this->cacheString .= ' '.implode(', ', $temp);
	}

	public function sendHeader() {
		$this->setCacheString();
		return header("Cache-Control:{$this->cacheString}");
	}
}
