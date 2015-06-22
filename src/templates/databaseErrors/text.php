<?php
use HTTP\Response\Status;
$status = Status::getInstance();
?>

status: <?php echo $status->getCode()."\n" ?>
description: <?php echo $status->getMessage()."\n"; ?>
message: Please try again later
