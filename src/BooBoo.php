<?php
namespace Exception;

use \HTTP\Response;
use \HTTP\Response\Status;
use \HTTP\Response\ContentType;

class BooBoo extends \Exception {

	/**
	 * The Error object
	 * @var MyBooBoos\Error
	 */
	public static $booboo;

	/**
	 * The logger object
	 * @var BooBoo\BooBooLog
	 */
	public static $logger;

	/**
	 * HTTP response handler
	 * @var HTTP\Response
	 */
	public static $httpHandler;

	/**
	 * Error levels
	 * @var array
	 */
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
		E_USER_DEPRECATED => 'User Deprecated',
		E_STRICT			=>	'Runtime Notice'
	);

	protected static $defaultErrorPath = [
		'text'	=>	__DIR__.'/templates/defaultErrors/text.php',
		'html'	=>	__DIR__.'/templates/defaultErrors/html.php',
		'json'	=>	__DIR__.'/templates/defaultErrors/json.php',
		'xml'	=>	__DIR__.'/templates/defaultErrors/xml.php'
	];

	/**
	 * Constructor
	 * @param MyBooBoos $booboo          A MyBooBoo object
	 * @param boolean|null   $statusCode      HTTP status code
	 */
	public function __construct(\MyBooBoos\Error $booboo, $statusCode = 200) {
		parent::__construct($booboo->getMessage());
		self::$booboo = $booboo;

		self::$httpHandler = self::$httpHandler->withStatus($statusCode);
	}

	/**
	 * Set up BooBoo.
	 * @param BooBooLog|null $logger [A BooBooLog to replace the default logger]
	 */
	final public static function setUp(\Psr\Log\LoggerInterface $logger = null) {
		ini_set('display_errors', 0);

		if(version_compare(PHP_VERSION, '5.3', '>=')) {
			error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
		}
		else {
			error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
		}

		set_exception_handler(array('Exception\BooBoo','exceptionHandler'));
		set_error_handler(array('Exception\BooBoo','errorHandler'));
		register_shutdown_function(array('Exception\BooBoo','shutdownFunction'));

		self::$httpHandler = (new Response())->withTypeNegotiation();

		if(is_null($logger)) {
			self::$logger = BooBooLogger::getInstance();
		}
		else {
			self::$logger = $logger;
		}

		/*
			Leaving this here becuase i want to use monolog or some psr..
		if(is_null($logger)) {
			self::$logger = new Monolog\Logger('test');
			$log->pushHandler(new StreamHandler('path/to/your.log', Logger::WARNING));
		}
		else {
			self::$logger = $logger;
		}*/
	}

	/**
	 * Set a new defaultErrorPath
	 *
	 * @param	 string $error 	The error path to be overwritten
	 * @param  string $path   A path to a folder containing default errors for BooBoo
	 */
	public static function defaultErrorPath($error, $path) {
		self::$defaultErrorPath[$error] = $path;
	}

	/**
	 * Get the contents of an error template
	 * @param  String $file [Path of the file]
	 * @param  mixed $data [Data to be used in the file. This may get deprecated]
	 * @return file
	 */
	protected static function getContents($file, $data = null) {
		ob_start();
		include($file);
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
	}

	/**
	 * Log the error. Typically called on the catch part of a try/catch
	 * @param  boolean $includeTrace [Include the strack trace or not]
	 */
	final public function log($includeTrace = true) {
		if($includeTrace) {
			self::$logger->log(self::booboo.": {$this->getMessage()} in {$this->getFile()} at line {$this->getLine()}. Stack trace: {$this->getTraceAsString()}");
		}
		else {
			self::$logger->log(self::booboo.": {$this->getMessage()} in {$this->getFile()} at line {$this->getLine()}.");
		}
	}

	/**
	 * Override the exception handler
	 */
	final public static function exceptionHandler($exception) {
		if(get_class($exception) !== __CLASS__) {
			self::$logger->log(get_class($exception).": {$exception->getMessage()} in {$exception->getFile()} at line {$exception->getLine()}. Stack trace: {$exception->getTraceAsString()}");

			$format = ContentType::getInstance()->getString();

			switch($format) {
				case ContentType::TEXT:
					self::$httpHandler->overwrite(self::getContents(self::$defaultErrorPath['text']));
					break;
				case ContentType::HTML:
					self::$httpHandler->overwrite(self::getContents(self::$defaultErrorPath['html']));
					break;
				case ContentType::JSON:
					self::$httpHandler->overwrite(self::getContents(self::$defaultErrorPath['json']));
					break;
				case ContentType::XML:
					self::$httpHandler->overwrite(self::getContents(self::$defaultErrorPath['xml']));
					break;
				default:
					self::$httpHandler->overwrite(self::getContents(self::$defaultErrorPath['text']));
					self::$logger->log("Error: Can't find template in the format compatible for {$format}. Defaulting to plain text");
			}

			self::$httpHandler->withStatus(Status::CODE500);
			self::$httpHandler->send();
		}
		else {
			self::$logger->log(self::$booboo.": {$exception->getMessage()} in {$exception->getFile()} at line {$exception->getLine()}. Stack trace: {$exception->getTraceAsString()}");
			self::$httpHandler->overwrite(self::$booboo->printErrorMessage(ContentType::getInstance()->getString()))->send();
		}
	}

	/**
	 * Override the shut down function
	 */
	final public static function shutdownFunction() {
		$last_error = error_get_last();

		if(isset($last_error) && ($last_error['type'] &
		(E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING))) {
			self::errorHandler($last_error['type'], $last_error['message'], $last_error['file'], $last_error['line']);
		}
	}

	/**
	 * Override the errorHandler
	 */
	final public static function errorHandler($severity, $message, $filepath, $line) {
		//var_dump($message, $filepath, $line);
		$is_error = (((E_ERROR | E_COMPILE_ERROR | E_CORE_ERROR | E_USER_ERROR) & $severity) === $severity);

		if ($is_error) {
			$format = ContentType::getInstance()->getString();

			switch($format) {
				case ContentType::TEXT:
					self::$httpHandler->overwrite(self::getContents(self::$defaultErrorPath['text']));
					break;
				case ContentType::HTML:
					self::$httpHandler->overwrite(self::getContents(self::$defaultErrorPath['html']));
					break;
				case ContentType::JSON:
					self::$httpHandler->overwrite(self::getContents(self::$defaultErrorPath['json']));
					break;
				case ContentType::XML:
					self::$httpHandler->overwrite(self::getContents(self::$defaultErrorPath['xml']));
					break;
				default:
					self::$httpHandler->overwrite(self::getContents(self::$defaultErrorPath['text']));
					self::$logger->log("Error: Can't find template in the format compatible for {$format}. Defaulting to plain text");
			}
		}

		if (($severity & error_reporting()) !== $severity) {
			return;
		}

		$level = self::$levels[$severity];

		if( ! in_array($severity, array(E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR), true)) {
			self::$logger->log("{$level}: {$message} in {$filepath} at line {$line}.");
		}

		if($is_error) {
			self::$httpHandler->withStatus(500)->send();
			exit(1);
		}
	}
}
