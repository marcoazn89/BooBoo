<?php
require '../vendor/autoload.php';

use Exception\BooBoo;

\HTTP\Support\TypeSupport::addSupport([
	//\HTTP\Response\ContentType::HTML,
	\HTTP\Response\ContentType::TEXT
]);

BooBoo::setUp(null, function() { error_log("testing callable");});

throw new \Exception\BooBoo(
	(new MyBooBoos\DatabaseError('The message for the client'))->enableLogging('The message for the logs'),
	(new \HTTP\Response())->withStatus(404)->withLanguage(\HTTP\Response\Language::DUTCH));

//fatal error
$a->o();

/* Warning
foreach($k as $v) {
	die("got away with it");
}*/

/* syntax error */
