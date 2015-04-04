<?php
require 'vendor/autoload.php';

use HTTP\HTTP;
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

TypeSupport::addSupport(array(
	ContentType::HTML,
	ContentType::JSON,
	ContentType::TEXT
	));

//You must pass format=<something> to test this
/*$format = $_GET['format'];

//You need to write this yourself
switch ($format) {
	case 'json':
		$format = array(ContentType::JSON);
		break;
	case 'html':
		$format = array(ContentType::HTML);
		break;
	case 'text':
		$format = array(ContentType::TEXT);
		break;
	default:
		$format = array();
		break;
}


AcceptType::setContent(true, $format);*/

//AcceptType::$content will output:
//array(1) { [0]=> string(16) "application/json" } 

//$content = ContentNegotiation::negotiate(AcceptType::$content, TypeSupport::getSupport());

$contentType = HTTP::negotiateContentType(false);

	switch($contentType) {
		case ContentType::JSON:
			echo json_encode(array('message' => 'A JSON response'));
			break;
		case ContentType::HTML:
			echo "<h3>message:</h3> An HTML response";
			break;
		case ContentType::TEXT:
			echo "message: A plain text response";
			break;
	}	




HTTP::contentType($contentType);

HTTP::sendResponse();
