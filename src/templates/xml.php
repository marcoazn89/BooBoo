<response>
	<status><?php echo $response->getStatusCode(); ?></status>
	<description><?php echo $response->getReasonPhrase(); ?></description>
	<message><?php if (!empty($message)) echo $message; else echo 'Please try again later'; ?></message>
</response>
