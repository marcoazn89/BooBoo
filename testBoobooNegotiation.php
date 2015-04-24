<?php
require 'vendor/autoload.php';

use HTTP\HTTP;
use HTTP\response\Status;
use HTTP\response\ContentType;
use HTTP\response\Language;
use HTTP\response\WWWAuthenticate;
use HTTP\response\CacheControl;
use HTTP\request\AcceptType;
use HTTP\request\AcceptLanguage;
use HTTP\support\TypeSupport;
use HTTP\support\ContentSupport;
use HTTP\support\LanguageSupport;
use HTTP\ContentNegotiation;
use BooBoo\BooBoo;
use BooBoo\MyBooBoos\DatabaseError;

BooBoo::setUp();
//HTTP::contentType(ContentType::TEXT);

throw new BooBoo(new DatabaseError(DatabaseError::NOT_AVAILABLE),500);

try {
	throw new BooBoo(new DatabaseError(DatabaseError::NOT_AVAILABLE), 300);
}
catch(BooBoo $poo) {
	$poo->log(false);
}

echo "ksdkdslklkfslk";
HTTP::sendResponse();


