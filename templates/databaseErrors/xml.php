<?php
use HTTP\response\Status;
$status = Status::getInstance();
?>

<response>
	<status><?php echo $status->code; ?></status>
	<description><?php echo $status->message; ?></description>
	<message>Please try again later</message>
</response>
