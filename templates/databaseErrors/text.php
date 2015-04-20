<?php
use HTTP\response\Status;
$status = Status::getInstance();
?>

status: <?php echo $status->code."\n" ?>
description: <?php echo $status->message."\n"; ?>
message: Please try again later
