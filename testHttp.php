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

HTTP::status(415);
HTTP::contentType(ContentType::MPEG_AUDIO);
HTTP::language(Language::KOREAN);
HTTP::authenticate('Digest', array('realm' => "MyApp", 'nonce' => "6cf093043215da528d7b5039ed4694d3", 'qop' => 'auth'));

$cache = new CacheControl();
$cache->setMode(CacheControl::CACHE_PUBLIC);
$cache->setExpirationType(CacheControl::MAX_AGE, 600);
$cache->setChannel('http://localhost:8080/channel/content');
$cache->setStale(CacheControl::STALE_IF_ERROR);
HTTP::cache($cache);

HTTP::sendResponse();
