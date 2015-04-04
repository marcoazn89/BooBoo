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
use BooBoo\BooBoo;
use BooBoo\MyBooBoos\DatabaseError;

BooBoo::setUp();
throw new BooBoo(new DatabaseError(420));
