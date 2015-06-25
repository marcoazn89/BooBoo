Install via Composer
---------------------
	composer require marcoazn89/http-wrapper:dev-master

Features
---------------------
* Dynamic error templates for production errors
* Content negotiation to show the right error format (HTML, JSON, XML, etc)
* PSR-3 compliant (set your own logger)
* Define your own error classes + constants for consistancy in your logs

Run BooBoo
------------------------------

```php
require '../vendor/autoload.php';

use BooBoo\BooBoo;

BooBoo::setUp();
```

Make it fail
------------------------------

```php
require '../vendor/autoload.php';

use BooBoo\BooBoo;

BooBoo::setUp();

$random->error();

```

![BooBoo!](http://i.imgur.com/OGIQDiP.png?1)
![BooBoo!](http://i.imgur.com/TXboLaP.png)

BooBoo pays attention to Accept headers
----------------------------------------
![BooBoo!](http://i.imgur.com/21kRZLp.png)

Set your own errrors
-----------------------------
It is suggested that you define constants for error message descriptions
to keep things consistant
```php
namespace BooBoo\MyBooBoos;

class DatabaseError extends Error {

	const NOT_AVAILABLE = 'Unable to connect to database';
	const BAD_QUERY = 'Query is not formatted properly';

	protected function getTEXT() {
		return __DIR__.'../../templates/DatabaseErrors/text.php';
	}

	protected function getHTML() {
		return __DIR__.'../../templates/DatabaseErrors/html.php';
	}

	protected function getXML() {
		return __DIR__.'../../templates/DatabaseErrors/xml.php';
	}

	protected function getJSON() {
		return __DIR__.'../../templates/DatabaseErrors/json.php';
	}

	public function getTag() {
		return "DatabaseError";
	}
}

```

Throw a BooBoo
---------------
Note the Accept header is now asking for xml
```php
require '../vendor/autoload.php';

use BooBoo\BooBoo;
use BooBoo\MyBooBoos\DatabaseError;

BooBoo::setUp();

// new BooBoo() takes 2 parameters:
// 1- An object of type Error (make sure you pass the defined constant)
// 2- HTTP code
throw new BooBoo(new DatabaseError(DatabaseError::NOT_AVAILABLE), 400);
```
![BooBoo!](http://i.imgur.com/yc0qwKp.png)