<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

require '../vendor/autoload.php';

use Exception\BooBoo;

\HTTP\Support\TypeSupport::addSupport([
	\HTTP\Response\ContentType::HTML
	//\HTTP\Response\ContentType::TEXT
]);


BooBoo::setUp(
  (new \Monolog\Logger('TEST'))->pushHandler(new \Monolog\Handler\StreamHandler(__DIR__.'/log', \Monolog\Logger::ERROR)),
  function() { error_log("testing callable");},
  [E_NOTICE, E_DEPRECATED]
);
//BooBoo::setUp();
//throw new Exception("FAIL");
//trigger_error("hahaha", E_USER_NOTICE);

throw new \Exception\BooBoo(
	new MyBooBoos\DatabaseError('The message for the client', 'The message for the logs', ['ip' => 12345]),
	(new \HTTP\Response())->withStatus(500)->withLanguage(\HTTP\Response\Language::DUTCH));

//fatal error
//$a->o();

// Warning
$k = [];
echo "hey".$k[0];
foreach($k as $v) {
	die("got away with it");
}

/* syntax error */
