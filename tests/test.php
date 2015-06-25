<?php
require '../vendor/autoload.php';

use BooBoo\BooBoo;

\HTTP\Support\TypeSupport::addSupport([
	\HTTP\Response\ContentType::HTML,
	\HTTP\Response\ContentType::TEXT
]);

BooBoo::setUp();

$a->o();