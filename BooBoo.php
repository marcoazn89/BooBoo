<?php
namespace BooBoo;

require_once('HTTP/HTTP.php');
require_once('BooBooLogger.php');

use HTTP\HTTP;
use BooBoo\BooBooLogger;
use Exception;
use HTTP\response\ContentType;
use BooBoo\MyBooBoos\MyBooBoos;

class BooBoo extends Exception {
	
	public static $logger;
	public static $boobooException;
	public static $levels = array(
		E_ERROR				=>	'Fatal Error',
		E_WARNING			=>	'Warning',
		E_PARSE				=>	'Parsing Error',
		E_NOTICE			=>	'Notice',
		E_CORE_ERROR		=>	'Core Error',
		E_CORE_WARNING		=>	'Core Warning',
		E_COMPILE_ERROR		=>	'Compile Error',
		E_COMPILE_WARNING	=>	'Compile Warning',
		E_USER_ERROR		=>	'User Error',
		E_USER_WARNING		=>	'User Warning',
		E_USER_NOTICE		=>	'User Notice',
		E_STRICT			=>	'Runtime Notice'
	);

	public function __construct(MyBooBoos $booboo, $isCatched = false, $disableCatchLog = false) {
		parent::__construct($booboo->getDescription());

		if($isCatched) {
			if( ! $disableCatchLog) {
				self::$logger->log("{$booboo}: {$booboo->getDescription()} in {$this->getFile()} at line {$this->getLine()}. Stack trace: {$this->getTraceAsString()}");
			}
		}
		else {
			self::$logger->log("{$booboo}: {$booboo->getDescription()} in {$this->getFile()} at line {$this->getLine()}. Stack trace: {$this->getTraceAsString()}");
			HTTP::body($booboo->printErrorMessage(ContentType::getInstance()->getContent()));
		}
	}

	final public static function setUp(BooBooLog $logger = null) {
		ini_set('display_errors', 0);

		if(version_compare(PHP_VERSION, '5.3', '>=')) {
			error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
		}
		else {
			error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
		}

		set_exception_handler(array('BooBoo\BooBoo','exceptionHandler'));
		set_error_handler(array('BooBoo\BooBoo','errorHandler'));
		register_shutdown_function(array('BooBoo\BooBoo','shutdownFunction'));

		HTTP::contentType(HTTP::negotiateContentType());

		if(is_null($logger)) {
			self::$logger = new BooBooLogger();
		}
		else {
			self::$logger = $logger;
		}
	}

	protected static function getContents($file, $data = null) {
		ob_start();
		include($file);
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
	}

	final public static function exceptionHandler($exception) {
		if(get_class($exception) !== __CLASS__) {
			self::$logger->log(get_class($exception).": {$exception->getMessage()} in {$exception->getFile()} at line {$exception->getLine()}. Stack trace: {$exception->getTraceAsString()}");
			
			switch(ContentType::getInstance()->getContent()) {
				case ContentType::TEXT:
					HTTP::body(self::getContents('templates/defaultErrors/text.php'));
					break;
				case ContentType::HTML:
					HTTP::body(self::getContents('templates/defaultErrors/html.php'));
					break;
				case ContentType::XML:
					HTTP::body(self::getContents('templates/defaultErrors/xml.php'));
					break;
				case ContentType::JSON:
					HTTP::body(self::getContents('templates/defaultErrors/json.php'));
					break;
			}
			HTTP::status(500);
			HTTP::sendResponse();
		}
		else {
			HTTP::sendResponse();
		}
	}

	final public static function shutdownFunction() {
		$last_error = error_get_last();
		

		if(isset($last_error) && ($last_error['type'] &
		(E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING))) {
			self::errorHandler($last_error['type'], $last_error['message'], $last_error['file'], $last_error['line']);
		}
	}

	final public static function errorHandler($severity, $message, $filepath, $line) {
		$is_error = (((E_ERROR | E_COMPILE_ERROR | E_CORE_ERROR | E_USER_ERROR) & $severity) === $severity);
		
		if ($is_error) {
			switch(ContentType::getInstance()->getContent()) {
				case ContentType::TEXT:
					HTTP::body(self::getContents('templates/defaultErrors/text.php'));
					break;
				case ContentType::HTML:
					HTTP::body(self::getContents('templates/defaultErrors/html.php'));
					break;
				case ContentType::XML:
					HTTP::body(self::getContents('templates/defaultErrors/xml.php'));
					break;
				case ContentType::JSON:
					HTTP::body(self::getContents('templates/defaultErrors/json.php'));
					break;
			}
		}

		if (($severity & error_reporting()) !== $severity) {
			return;
		}
		
		$level = self::$levels[$severity];

		if( ! in_array($severity, array(E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR), true)) {
			self::$logger->log("{$level}: {$message} in {$filepath} at line {$line}.");
		}

		if ($is_error) {
			HTTP::status(500);
			HTTP::sendResponse();
			exit(1);
		}
	}
}
