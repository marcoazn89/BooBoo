<?php
namespace HTTP\response;

abstract class HTTPHeader {
	protected $headerString = '';

	//Singleton Pattern: Can't create an instance
	protected function __construct() {}
	//Singleton Pattern: Can't clone
	protected function __clone() {}
	//Singleton Pattern: Can't deserialize
	protected function __wakeup() {}

	/**
	 * Set values of instance
	 * @param mixed  $value [Values for the header]
	 * @param boolean $send  [Send header right away]
	 * @return void
	 */
	abstract public function set($value, $send = false);

	/**
	 * Send header string
	 */
	final public function sendHeader() {
		header($this->headerString);
	}
	
	/**
	 * Get the header instance
	 * @return HttpHeader
	 */
	final public static function getInstance() {
		static $instance = null;

		if(is_null($instance)) {
			$instance = new static();
		}

		return $instance;
	}
}
