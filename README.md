Install via Composer
---------------------
	composer require marcoazn89/booboo:dev-master

Features
---------------------
* Dynamic error templates for production errors
* Content negotiation to show the right error format (HTML, JSON, XML, etc)
* PSR-3 compliant (set your own logger)
* PSR-7 compliant (pass a response object with headers and status code)

Run BooBoo
------------------------------
This is something you want to do at the begining of your application.

```php
require '../vendor/autoload.php';

use Exception\BooBoo;

// This is a simple set up
BooBoo::setUp();
```
The setUp() method takes 4 parameters:

1) $logger: BooBoo will use error_log by default. If you want to use your own you can pass a psr2 logger compliant like [`Monolog`](https://github.com/Seldaek/monolog).

2) $traceAlwaysOn: Turn of or off stack traces. By default they are turned off. You can always turn it on or off later when you throw an exception, but any other regular exception or php error will use the setting defined at the set up

3) $lastAction: This is a callback that will be ran before exiting the application upon encountering an error

4) $ignore: This is an array containing php error constants that you want to ignore.

```php
// A more complex set up
BooBoo::setUp(
	$logger,
	true,
	function() { $textMessageService->send('Hey something broke');},
	[E_NOTICE, E_DEPRECATED]
);
```

What happens when an error occurs?
-----------------------------------

```php
require '../vendor/autoload.php';

use Exception\BooBoo;

BooBoo::setUp();

// This causes a fatal error
$random->error();

```

![BooBoo!](http://i.imgur.com/OGIQDiP.png?1)
![BooBoo!](http://i.imgur.com/TXboLaP.png)

BooBoo pays attention to Accept headers
----------------------------------------
Client requesting JSON

![BooBoo!](http://i.imgur.com/21kRZLp.png)

Client requesting XML

![BooBoo!](http://i.imgur.com/yc0qwKp.png)

Creating exceptions and templates
------------------------------------
1) Simply extend ```\Exception\BooBoo``` and implement the two abstract methods
```php
class DatabaseException extends \Exception\BooBoo
{
	// It's good practice to define constants so that your exception messages are consistant
	const NOT_FOUND = 'Data requested was not found';
	
	/**
	 * This will be what's shown in the logs
	 * Example: [21-Mar-2016 05:58:27 Europe/Berlin] <TAG>: Something bad happened
	 */
	protected function getTag()
	{
		return 'DbException';
	}
	
	/**
	 * Return an array that defines your error template location or strings.
	 * For any template that is not defined, BooBoo will use its default templates.
	 * There are 4 templates currently supported: html, text, xml, json
	 */
	protected function getTemplates()
	{
		return [
			'html' => "<p>Something went really <h1>WRONG!</h1></p>",
			'json' => __DIR__ . '/json.php'
		];
	}
}
```
2) Templates get the following varibles injected:
* $response: The psr7 response object
* $message: The message that was defined for the template or null if none was provided
* $data: Any data that was passed to the template or null if none was provided

```php
<?php
// Let's use json.php that was defined as a template in the previous example
{
    "status": <?php echo $response->getStatusCode();?>,
    "description": "<?php echo $response->getReasonPhrase(); ?>",
    "message": "<?php if (!empty($data)) echo $data; else echo 'Please try again later'; ?>",
    "data": <?php if (!empty($data)) echo json_encode($data); else echo null; ?>
}
```

3) Throw the exception
```php
// Lets use DatabaseException defined above for this example
// Note that the exceptions you define still work as a regular exception so you can just do a simple throw like:
throw new DatabaseException('Data was not found. The table appears to be empty');

// Or (showing all the options for documentation purposes)
throw (new DatabaseException('Data was not found. The table appears to be empty'))
	//Passing a response object with 400 status code.
	//Please not that BooBoo ignores any status code below 400 and turn it into 500
	->response($response->withStatus(400))
	
	//Use the constant defined for the message displayed to the client
	->displayMessage(DatabaseException::NOT_FOUND)
    	
    	//Add context to your logs
    	->logContext(['sessionID' => 12345])
    	
    	//Pass data to your template
    	->templateData(['actions' => 'Contact support'])
    	
    	// Turn off stack traces
    	->trace(false);
    	
    	// Tur off logging
    	->noLog();
```

Set limits on what you can support
-----------------------------------
The order in which you add support matters! This will ignore any Accept
headers that don't match the supported types. Learn more from [`http-wrapper`](https://github.com/marcoazn89/http-wrapper/tree/v2.0)
```php
require '../vendor/autoload.php';

use BooBoo\BooBoo;

// Add supported types
\HTTP\Support\TypeSupport::addSupport([
	\HTTP\Response\ContentType::HTML,
	\HTTP\Response\ContentType::TEXT
]);

BooBoo::setUp();

// Assume the client sends a text Accept header, the response will
// be in plain text because is the best match. If none match, the
// response will be in HTML because it was the first one added
$random->error();
```

How BooBoo connects errors with HTTP responses:
------------------------------------------------
* Any HTTP 500+ status code and fatal error will translate into a critical log level because they are system errors.
* Any HTTP status code in the 400 range will translate into a warning log level because they are client generated errors. This only applies for the exceptions you define by extending BooBoo.
* Any php non fatal error or message will translate into an error log level.
