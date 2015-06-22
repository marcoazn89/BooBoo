<?php
use HTTP\Response\Status;
$status = Status::getInstance();
?>

<response>
	<status><?php echo $status->getCode(); ?></status>
	<description><?php echo $status->getMessage(); ?></description>
	<message>Please try again later</message>
</response>
