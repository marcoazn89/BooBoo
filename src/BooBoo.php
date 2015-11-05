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
	 * Last action to be executed before the script ends
	 * @var bindToable
	 */
	public static $lastAction;

	public static $vars = [];

	/**
	 * Error levels
	 * @var array
	 */
	public static $levels = [
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
		E_STRICT			=>	'Runtime Notice',
		E_RECOVERABLE_ERROR => 'Recoverable Error'
	];

	protected static $defaultErrorPath;

	protected static $ignore = [];

	protected static $alwaysLog = true;

	protected static $settings = [
		'alwaysLog'					=>	false,
		'ignore'						=>	[],
		'defaultErrorPaths'	=>	[],
		'lastAction'				=>	null
	];

	/**
	 * Constructor
	 * @param MyBooBoos $booboo          A MyBooBoo object
	 * @param boolean|null   $statusCode      HTTP status code
	 */
	public function __construct(\MyBooBoos\ErrorTemplate $booboo, \Psr\Http\Message\ResponseInterface $response = null) {
		// don't really need to pass getMessage() because we never do $exception->getMessage()
		parent::__construct($booboo->getMessage());
		self::$booboo = $booboo;

		if(!isset(self::$httpHandler)) {
			self::setUp();
		}

		if(is_null($response)) {
			self::$httpHandler = self::$httpHandler->withStatus(200);
		}
		else {
			self::$httpHandler = self::$httpHandler->withStatus($response->getStatusCode());

			foreach($response->getHeaders() as $header => $value) {
				self::$httpHandler = self::$httpHandler->withHeader($header, $value);
			}
		}
	}

	/**
	 * Set up BooBoo.
	 * @param \Psr\Log\LoggerInterface|null  $logger       A psr3 compatible logger
	 * @param \Closure                       $lastAction   Last action to run before script ends
	 * @param int                            $reportLevel  Level of reporting. One can pass a bitmask here
	 */
	final public static function setUp(\Psr\Log\LoggerInterface $logger = null, \Closure $lastAction = null, $ignore = []) {
		ini_set('display_errors', 0);

		/*if(version_compare(PHP_VERSION, '5.3', '>=')) {
			error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
		}
		else {
			error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
		}*/

		error_reporting(E_ALL - array_sum($ignore));

		self::$ignore = $ignore;

		self::$defaultErrorPath = [
			'text'	=>	__DIR__.'/templates/defaultErrors/text.php',
			'html'	=>	__DIR__.'/templates/defaultErrors/html.php',
			'json'	=>	__DIR__.'/templates/defaultErrors/json.php',
			'xml'	=>	__DIR__.'/templates/defaultErrors/xml.php'
		];

		self::$httpHandler = (new Response())->withTypeNegotiation();

		/*if(empty($logger)) {
			self::$logger = (new \Monolog\Logger('PHP_ERROR'))->pushHandler(new \Monolog\Handler\StreamHandler(ini_get('error_log'), \Monolog\Logger::ERROR));
		}
		else {
			self::$logger = $logger;
		}*/

		self::$logger = $logger;
		self::$lastAction = $lastAction;

		set_exception_handler(['Exception\BooBoo','exceptionHandler']);
		set_error_handler(['Exception\BooBoo','errorHandler']);
		register_shutdown_function(['Exception\BooBoo','shutdownFunction']);
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

	public static function addVars(array $vars) {
		return array_merge(self::$vars, $vars);
	}

	public static function resetVars() {
		return self::$vars = [];
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

	protected static function getExceptionMsg($exception, $booboo, $message) {
		$log = "";

		$log = $booboo->getTag().": {$message} in {$exception->getFile()} in line {$exception->getLine()}.";

		if($booboo->fullTrace()) {
			$log .= "\nStack trace:\n{$exception->getTraceAsString()}";
		}
		else {
			$trace = $exception->getTrace();

			$origin = empty($trace[0]['file']) ? '' : "{$trace[0]['file']}({$trace[0]['line']}): ";

			$log .= "\nOriginated at: {$origin}{$trace[0]['class']}{$trace[0]['type']}{$trace[0]['function']}()";
		}

		return $log;
	}

	/**
	 * Override the exception handler
	 */
	final public static function exceptionHandler($exception) {
		if(get_class($exception) !== __CLASS__) {
			if(!empty(self::$logger)) {
				self::$logger->critical(get_class($exception).": {$exception->getMessage()} in {$exception->getFile()} in line {$exception->getLine()}.\nStack trace:\n{$exception->getTraceAsString()}", self::$vars);
			}
			else {
				error_log(get_class($exception).": {$exception->getMessage()} in {$exception->getFile()} in line {$exception->getLine()}.\nStack trace:\n{$exception->getTraceAsString()}", self::$vars);
			}

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
					error_log("Error: Can't find template in the format compatible for {$format}. Defaulting to plain text");
			}

			if(!is_null(self::$lastAction)) {
				$fn = self::$lastAction;
				$fn();
			}

			self::$httpHandler->withStatus(500);
			self::$httpHandler->send();
		}
		else {
			if(!empty($message = $exception->getMessage()) || self::$alwaysLog) {
				$message = empty($message) ? self::$booboo->getData() : $message;

				if(Status::getInstance()->getCode() >= 500) {
					if(!empty(self::$logger)) {
						self::$logger->critical(self::getExceptionMsg($exception, self::$booboo, $message), array_merge(self::$booboo->getContext(), self::$vars));
					}
					else {
						error_log(self::getExceptionMsg($exception, self::$booboo, $message), array_merge(self::$booboo->getContext(), self::$vars));
					}
				}
				else {
					if(!empty(self::$logger)) {
						self::$logger->warning(self::getExceptionMsg($exception, self::$booboo, $message), array_merge(self::$booboo->getContext(), self::$vars));
					}
					else {
						error_log(self::getExceptionMsg($exception, self::$booboo, $message), array_merge(self::$booboo->getContext(), self::$vars));
					}
				}
				//error_log(self::$booboo->getTag().": {$exception->getMessage()} in {$exception->getFile()} in line {$exception->getLine()}.\nStack trace:\n{$exception->getTraceAsString()}");
			}

			if(!is_null(self::$lastAction)) {
				$fn = self::$lastAction;
				$fn();
			}

			self::$httpHandler->overwrite(self::$booboo->printErrorMessage(ContentType::getInstance()->getString()))->send();
		}
	}

	/**
	 * Override the shut down function
	 */
	final public static function shutdownFunction() {
		$last_error = error_get_last();

		if(isset($last_error) && ($last_error['type'] &
		(E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING | E_RECOVERABLE_ERROR))) {
			self::errorHandler($last_error['type'], $last_error['message'], $last_error['file'], $last_error['line']);
		}
	}

	/**
	 * Override the errorHandler
	 */
	final public static function errorHandler($severity, $message, $filepath, $line) {
		$is_error = (((E_ERROR | E_COMPILE_ERROR | E_CORE_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR) & $severity) === $severity);

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
					//self::$logger->log("Error: Can't find template in the format compatible for {$format}. Defaulting to plain text");
					// Should eventually put this error in booboo's own log maybe?
					error_log("BooBoo: Can't find template in the format compatible for {$format}. Defaulting to plain text");
			}
		}

		$level = self::$levels[$severity];

		if(!in_array($severity, array_merge([E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR], self::$ignore), true)) {

			error_log("{$level}: {$message} in {$filepath} in line {$line}");

			if(!empty(self::$logger)) {
				self::$logger->error("{$level}: {$message} in {$filepath} in line {$line}", self::$vars);
			}
		}

		if($is_error) {
			if(!empty(self::$logger)) {
				self::$logger->critical("{$level}: {$message} in {$filepath} in line {$line}", self::$vars);
			}
			//else {
			//error_log("{$level}: {$message} in {$filepath} in line {$line}");
			//}

			if(!is_null(self::$lastAction)) {
				$fn = self::$lastAction;
				$fn();
			}

			self::$httpHandler->withStatus(500)->send();
			exit(1);
		}
	}
}
