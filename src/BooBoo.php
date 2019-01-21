<?php
namespace Exception;

use \HTTP\Response;
use \HTTP\Header\ContentType;
use \Psr\Http\Message\ResponseInterface;

abstract class BooBoo extends \Exception
{
	const BOOBOO = 1;
	const EXCEPTION = 2;
	const ERROR = 3;

	/**
	 * The logger object
	 * @var BooBoo\BooBooLog
	 */
	protected static $logger;

	/**
	 * HTTP response handler
	 * @var HTTP\Response
	 */
	protected static $httpHandler;

	/**
	 * Last action to be executed before the script ends
	 * @var bindToable
	 */
	protected static $lastAction;

	protected static $trace;

	protected static $vars = [];

	protected static $exit = true;

	/**
	 * Error levels
	 * @var array
	 */
	protected static $levels = [
		E_ERROR				=>	'Fatal Error',
		E_WARNING			=>	'Warning',
		E_PARSE				=>	'Parsing Error',
		E_NOTICE			=>	'Notice',
		E_DEPRECATED 		=>	'Deprecated',
		E_CORE_ERROR		=>	'Core Error',
		E_CORE_WARNING		=>	'Core Warning',
		E_COMPILE_ERROR		=>	'Compile Error',
		E_COMPILE_WARNING	=>	'Compile Warning',
		E_USER_ERROR		=>	'User Error',
		E_USER_WARNING		=>	'User Warning',
		E_USER_NOTICE		=>	'User Notice',
		E_USER_DEPRECATED 	=> 'User Deprecated',
		E_STRICT			=>	'Runtime Notice',
		E_RECOVERABLE_ERROR => 'Recoverable Error'
	];

	protected static $defaultErrorPath;

	protected static $ignore = [];

	protected static $alwaysLog = true;

	protected static $booboo;

	public function __construct($message = null, $code = 0, Exception $previous = null)
	{
		parent::__construct($message, $code, $previous);

		if (!isset(self::$httpHandler)) {
			self::setUp();
		}

		$this->buildObj();
	}

	protected function buildObj()
	{
		self::$booboo = new \StdClass;
		self::$booboo->tag = $this->getTag();
		self::$booboo->alwaysLog = true;
		self::$booboo->displayMessage = null;
		self::$booboo->logContext = [];
		self::$booboo->trace = false;
		self::$booboo->templateData = null;
		self::$booboo->httpHandler = (new Response())->withTypeNegotiation()->withStatus(500);
		self::$booboo->templates = array_merge(self::$defaultErrorPath, $this->getTemplates());
	}

	/**
	 * Get error tag that will appear in the error logs.
	 * Example: <tag>: Something went wrong
	 * @return String [Tag name]
	 */
	abstract protected function getTag();

	protected function getTemplates()
	{
		return [];
	}

	/**
	 * Set up BooBoo.
	 * @param \Psr\Log\LoggerInterface|null  $logger       A psr3 compatible logger
	 * @param \Closure                       $lastAction   Last action to run before script ends
	 * @param int                            $reportLevel  Level of reporting. One can pass a bitmask here
	 */
	final public static function setUp(\Psr\Log\LoggerInterface $logger = null, $traceAlwaysOn = false, \Closure $lastAction = null, $ignore = [])
	{
		ini_set('display_errors', 0);

		error_reporting(E_ALL - array_sum($ignore));

		self::$ignore = $ignore;

		self::$defaultErrorPath = [
			'text'	=>	__DIR__ . '/templates/text.php',
			'html'	=>	__DIR__ . '/templates/html.php',
			'json'	=>	__DIR__ . '/templates/json.php',
			'xml'	=>	__DIR__ . '/templates/xml.php'
		];

		self::$httpHandler = (new Response())->withTypeNegotiation()->withStatus(500);

		self::$logger = $logger;
		self::$trace = $traceAlwaysOn;
		self::$lastAction = $lastAction;

		set_exception_handler(['Exception\BooBoo','exceptionHandler']);
		set_error_handler(['Exception\BooBoo','errorHandler']);
		register_shutdown_function(['Exception\BooBoo','shutdownFunction']);
	}

	public static function alwaysExit($value)
	{
		self::$exit = $value;
	}

	public function noLog()
	{
		self::$booboo->alwaysLog = false;

		return $this;
	}

	public function displayMessage($message)
	{
		self::$booboo->displayMessage = $message;

		return $this;
	}

	public function logContext($context)
	{
		self::$booboo->logContext = $context;

		return $this;
	}

	public function trace($turnOnTrace)
	{
		self::$booboo->trace = $turnOnTrace;

		return $this;
	}

	public function status($code)
	{
		if ($code < 400) {
			throw new \InvalidArgumentException('Status code must be higuer or equal to 400');
		}

		self::$booboo->httpHandler->withStatus($code);

		return $this;
	}

	public function response(ResponseInterface $response)
	{
		self::$booboo->httpHandler = self::$booboo->httpHandler->withStatus(
			$response->getStatusCode() < 400 ? 500 : $response->getStatusCode()
		);

		foreach ($response->getHeaders() as $header => $value) {
			self::$booboo->httpHandler = self::$booboo->httpHandler->withHeader($header, implode(',', $value));
		}

		return $this;
	}

	public function templateData($data)
	{
		self::$booboo->templateData = $data;

		return $this;
	}

	/**
	 * Set a new defaultErrorPath
	 *
	 * @param	 string $error 	The error path to be overwritten
	 * @param  string $path   A path to a folder containing default errors for BooBoo
	 */
	public static function defaultErrorPath($error, $path)
	{
		self::$defaultErrorPath[$error] = $path;
	}

	public static function addVars(array $vars)
	{
		self::$vars = array_merge(self::$vars, $vars);
	}

	public static function resetVars()
	{
		self::$vars = [];
	}

	/**
	 * Get the contents of an error template
	 * @param  String $file [Path of the file]
	 * @param  mixed $data [Data to be used in the file. This may get deprecated]
	 * @return file
	 */
	public static function getContents($content, $response, $message, $data)
	{
		ob_start();

		if (is_file($content)) {
			include($content);
		} else {
			echo $content;
		}

		$buffer = ob_get_contents();
		ob_end_clean();

		return $buffer;
	}

	public static function getErrorTemplate($response, $templates, $message = null, $data = null)
	{
		switch ($response->getHeaderLine(ContentType::name())) {
			case ContentType::TEXT:
				return self::getContents($templates['text'], $response, $message, $data);
				break;
			case ContentType::HTML:
				return self::getContents($templates['html'], $response, $message, $data);
				break;
			case ContentType::XML:
				return self::getContents($templates['xml'], $response, $message, $data);
				break;
			case ContentType::JSON:
				return self::getContents($templates['json'], $response, $message, $data);
				break;
			default:
				error_log("Error: Can't find template in the format compatible for " . $response->getHeaderLine(ContentType::name()) . ". Defaulting to plain text");
				return self::getContents(self::getText(), $data);
		}
	}

	public static function getExceptionMsg($tag, $exception, $trace = false)
	{
		$log = "";

		$log = $tag . ": {$exception->getMessage()} in {$exception->getFile()} in line {$exception->getLine()}.";

		if ($trace) {
			$log .= "\nStack trace:\n{$exception->getTraceAsString()}";
		}

		return $log;
	}

	public static function getContext($type, $tag = null, $message, $file = null, $line = null, $code = null)
	{
		$error = [
			'error' => [
				'code'     => $code,
				'location' => [
				  'file' => $file,
				  'line' => $line
				],
				'message'  => $message,
				'tag'      => $tag
			]
		];

		switch($type) {
			case self::BOOBOO:
				return array_merge(
					empty(self::$booboo->logContext) ? [] : self::$booboo->logContext,
					self::$vars,
					$error
				);
			case self::EXCEPTION:
			case self::ERROR:
				return array_merge(
					self::$vars,
					$error
				);
			default:
				throw new \InvalidArgumentException('Incorrect type passed for setContext');
		}
	}

	public static function cleanUp()
	{
		$vars = [
			self::$alwaysLog,
			self::$logContext,
			self::$trace,
			self::$httpHandler,
			self::$displayMessage,
			self::$templateData,
			self::$lastAction
		];

		self::$alwaysLog = true;
		self::$logContext = [];
		self::$trace = false;
		self::$httpHandler;
		self::$displayMessage = null;
		self::$templateData = null;
		self::$lastAction = null;

		return $vars;
	}

	/**
	 * Override the exception handler
	 */
	final public static function exceptionHandler($exception)
	{
		$response = null;

		if (!$exception instanceof \Exception\BooBoo) {
			$class = get_class($exception);
			$msg = preg_replace("~[\r\n]~", ' ', $exception->getMessage());

			$context = self::getContext(self::EXCEPTION, $class, $msg, $exception->getFile(), $exception->getLine(), $exception->getCode());

			if (!empty(self::$logger)) {
				self::$logger->critical($class.": {$msg} in {$exception->getFile()} in line {$exception->getLine()}.", $context);
			}
			else {
				error_log(self::getExceptionMsg($class, $exception, self::$trace));
			}

			if (!is_null(self::$lastAction)) {
				$fn = self::$lastAction;
				$fn();
			}

			$response = self::$httpHandler->overwrite(
				self::getErrorTemplate(self::$httpHandler, self::$defaultErrorPath)
			);
		}
		else {
			if (self::$booboo->alwaysLog || self::$alwaysLog) {
				$context = self::getContext(self::BOOBOO, self::$booboo->tag, $exception->getMessage(), $exception->getFile(), $exception->getLine());

				$logMsg = self::getExceptionMsg(self::$booboo->tag, $exception, self::$booboo->trace);

				if (self::$booboo->httpHandler->getStatusCode() >= 500) {
					if (!empty(self::$logger)) {
						self::$logger->critical($logMsg, $context);
					}
					else {
						error_log($logMsg);
					}
				}
				else {
					if (!empty(self::$logger)) {
						self::$logger->warning($logMsg, $context);
					}
					else {
						error_log($logMsg);
					}
				}
			}

			if (!is_null(self::$lastAction)) {
				$fn = self::$lastAction;
				$fn();
			}

			/*self::$booboo->httpHandler->overwrite(
				self::getErrorTemplate(
					self::$booboo->httpHandler,
					self::$booboo->templates,
					self::$booboo->displayMessage,
					self::$booboo->templateData
				)
			)->send();*/

			$response = self::$booboo->httpHandler->overwrite(
				self::getErrorTemplate(
					self::$booboo->httpHandler,
					self::$booboo->templates,
					self::$booboo->displayMessage,
					self::$booboo->templateData
				)
			);
		}

		if (self::$exit) {
			$response->send();
		} else {
			return $response;
		}
	}

	/**
	 * Override the shut down function
	 */
	final public static function shutdownFunction()
	{
		$last_error = error_get_last();

		if (isset($last_error) && ($last_error['type'] &
		(E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING | E_RECOVERABLE_ERROR))) {
			self::errorHandler($last_error['type'], $last_error['message'], $last_error['file'], $last_error['line']);
		}
	}

	/**
	 * Override the errorHandler
	 */
	final public static function errorHandler($severity, $message, $filepath, $line)
	{
		$is_error = (((E_ERROR | E_COMPILE_ERROR | E_CORE_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR) & $severity) === $severity);

		$level = self::$levels[$severity];

		$context = self::getContext(self::ERROR, $level, $message, $filepath, $line);

		if (!in_array($severity, array_merge([E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR], self::$ignore), true)) {

			error_log("{$level}: {$message} in {$filepath} in line {$line}");

			if (!empty(self::$logger)) {
				self::$logger->error("{$level}: {$message} in {$filepath} in line {$line}", $context);
			}
		}

		if ($is_error) {
			if (!empty(self::$logger)) {
				self::$logger->critical("{$level}: {$message} in {$filepath} in line {$line}", $context);
			}

			if (!is_null(self::$lastAction)) {
				$fn = self::$lastAction;
				$fn();
			}

			self::$httpHandler->overwrite(self::getErrorTemplate(self::$httpHandler, self::$defaultErrorPath))->withStatus(500)->send();
		}
	}
}
