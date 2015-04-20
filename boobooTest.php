<?php
require 'vendor/autoload.php';

use HTTP\HTTP;
use BooBoo\BooBoo;
use HTTP\response\ContentType;
use BooBoo\MyBooBoos\DatabaseError;

HTTP::contentType(ContentType::XML);
BooBoo::setUp();
//throw new BooBoo(new DatabaseError(500));
throw new Exception("Error Processing Request", 1);


//$a++;
//
//
//e();
/*$k = null;
foreach($k as $t) {
	echo $t;
}*/

function a() {
	b();
}

function b() {
	c();
}

function c() {
	try {
		throw new BooBoo(new DatabaseError(), true);
		
	} catch (BooBoo $e) {
		echo "HAHAHA";
	}
	//throw new BooBoo(new DatabaseError());
	//yo();
}

a();

//trigger_error("eee", E);
//class Test extends Exception {}
//throw new Test("zooom");
//throw new BooBoo(new DatabaseError());
/*try {
	throw new BooBoo(new DatabaseError(), true);
	
} catch (Exception $e) {
	
}*/


