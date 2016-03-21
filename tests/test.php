<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

require '../vendor/autoload.php';

use Exception\BooBoo;

\HTTP\Support\TypeSupport::addSupport([
	//\HTTP\Header\ContentType::HTML,
	//\HTTP\Header\ContentType::TEXT
]);

class APIException extends \Exception\BooBoo
{
    const BAD_CALL = 'Incorrect use of API';

    protected function getTag()
    {
        return 'ApiError';
    }

    protected function getTemplates()
    {
        return [
            //'text' => 'Sorry, error happened!',
            'json' => __DIR__ . '/../src/templates/json.php'
        ];
    }
}

$logger = (new \Monolog\Logger('TEST'))
  ->pushHandler(
    new \Monolog\Handler\FingersCrossedHandler(
      new \Monolog\Handler\StreamHandler(__DIR__.'/log'),
      \Monolog\Logger::WARNING
    )
  )
  ->pushHandler(
    new \Monolog\Handler\FilterHandler(
      new \Monolog\Handler\StreamHandler(__DIR__.'/error.log', \Monolog\Logger::DEBUG),
      \Monolog\Logger::DEBUG,
      \Monolog\Logger::NOTICE
    )
  );

BooBoo::setUp(
  $logger,
  true,
  function() { error_log("testing callable");},
  [E_NOTICE, E_DEPRECATED]
);
//BooBoo::setUp();
//throw new Exception("FAIL");
//trigger_error("hahaha", E_USER_NOTICE);

$logger->debug("DEBBUGGINGGGGGGGGGGGGG");
$logger->notice("this will only appear in the logs when there's an error higuer or equal to a \Monolog\Logger::WARNING");
//$logger->warning("this will only appear in the logs when there's an error higuer or equal to a \Monolog\Logger::WARNING");

BooBoo::addVars(['userAgent' => 'mine']);

//try {
  throw (new APIException(APIException::BAD_CALL))
    ->response((new \HTTP\Response())->withStatus(400))
    ->displayMessage("BAHAHAHAHA")
    ->logContext(['bananas' => 'aaa'])
    ->templateData(['a' => 'b'])
    ->trace(false);
//} catch (\Exception $e) {
  throw new \Exception('mmm');
//}

//fatal error
$a->o();

// Warning
$k = [];
echo "hey".$k[0];
foreach($k as $v) {
	die("got away with it");
}

/* syntax error */
