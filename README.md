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
use BooBoo\MyBooBoos\DatabaseError;

BooBoo::setUp();

$random->error();

```

![BooBoo!](http://i.imgur.com/OGIQDiP.png?1)


