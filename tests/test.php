<?php
require '../vendor/autoload.php';

HTTP\Support\TypeSupport::addSupport([
	HTTP\Response\ContentType::JSON
	]);

\BooBoo\BooBoo::setUp();
//var_dump(\HTTP\Request\AcceptType::getContent());
//\HTTP\Response\ContentType::getInstance()->set(\HTTP\Response\ContentType::JSON);

//throw new Exception("Error Processing Request", 1);
throw new \BooBoo\BooBoo(new \BooBoo\MyBooBoos\DatabaseError(\BooBoo\MyBooBoos\DatabaseError::NOT_AVAILABLE),400);